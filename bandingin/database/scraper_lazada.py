"""
Scraper Lazada — Playwright Version (Perbaikan)
================================================
Cara Pakai:
  python scraper_lazada.py "iphone 15"
  python scraper_lazada.py "ps5 slim" --debug
"""
import sys
import os
import time
import random
from playwright.sync_api import sync_playwright

from db_helper import is_valid_product, parse_price, save_to_mysql


USER_AGENT = (
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
    "AppleWebKit/537.36 (KHTML, like Gecko) "
    "Chrome/126.0.0.0 Safari/537.36"
)

# JS yang lebih agresif untuk Lazada — menggunakan 3 strategi berbeda
EXTRACT_JS = r"""
() => {
    const products = [];
    const seen = new Set();

    // ============ STRATEGI 1: Link dengan title ============
    const productLinks = document.querySelectorAll('a[title][href*="lazada.co.id"]');
    
    productLinks.forEach(link => {
        let name = link.title || '';
        if (!name || name.length < 5) return;
        
        // Naik ke parent card untuk mencari harga
        let card = link.parentElement;
        for (let i = 0; i < 8; i++) {
            if (!card) break;
            const text = card.innerText || '';
            if (text.includes('Rp') && text.length > 20) break;
            card = card.parentElement;
        }
        
        if (!card) return;
        
        let priceStr = '';
        const cardText = card.innerText || '';
        const priceMatches = cardText.match(/Rp\s*([\d.]+)/g);
        if (priceMatches && priceMatches.length > 0) {
            priceStr = priceMatches[0].replace(/[^0-9]/g, '');
        }
        
        let href = link.href.split('?')[0];
        let imgEl = card.querySelector('img');
        let img = imgEl ? (imgEl.src || imgEl.getAttribute('data-src') || '') : '';
        
        if (name && priceStr && parseInt(priceStr) > 1000 && !seen.has(name)) {
            seen.add(name);
            products.push({ name, price: parseInt(priceStr), image_url: img, link: href });
        }
    });

    // ============ STRATEGI 2: data-qa-locator ============
    if (products.length < 5) {
        const cards = document.querySelectorAll('[data-qa-locator="product-item"], .Bm3ON, .qmXQo');
        cards.forEach(el => {
            const linkEl = el.querySelector('a');
            if (!linkEl) return;
            let name = linkEl.title || linkEl.textContent.trim().split('\n')[0];
            let href = linkEl.href.split('?')[0];
            
            let priceStr = '';
            const text = el.innerText || '';
            const pm = text.match(/Rp\s*([\d.]+)/g);
            if (pm) priceStr = pm[0].replace(/[^0-9]/g, '');
            
            let imgEl = el.querySelector('img');
            let img = imgEl ? (imgEl.src || '') : '';
            
            if (name && priceStr && parseInt(priceStr) > 1000 && !seen.has(name)) {
                seen.add(name);
                products.push({ name, price: parseInt(priceStr), image_url: img, link: href });
            }
        });
    }

    // ============ STRATEGI 3: Generic - semua <a> dengan gambar & Rp ============
    if (products.length < 5) {
        const allLinks = document.querySelectorAll('a[href*="lazada.co.id"]');
        allLinks.forEach(a => {
            const href = a.href;
            // Skip link yang bukan produk
            if (href.includes('/search') || href.includes('/catalog') || 
                href.includes('/help') || href.includes('/customer')) return;
            // Link produk Lazada biasanya: lazada.co.id/products/nama-produk-i123456.html
            if (!href.includes('-i') && !href.includes('/products/')) return;
            
            let card = a;
            for (let i = 0; i < 6; i++) {
                if (!card.parentElement) break;
                card = card.parentElement;
                const txt = card.innerText || '';
                if (txt.includes('Rp') && txt.split('\n').length >= 3) break;
            }
            
            const text = card.innerText || '';
            const lines = text.split('\n').map(l => l.trim()).filter(Boolean);
            
            let name = '';
            let priceStr = '';
            
            for (const line of lines) {
                if (!priceStr && /Rp[\d.,]+/.test(line)) {
                    priceStr = line.replace(/[^0-9]/g, '');
                } else if (!name && line.length > 10 && !/^Rp|^-?\d+%|^★/.test(line)) {
                    name = line;
                }
            }
            
            let imgEl = card.querySelector('img');
            let img = imgEl ? (imgEl.src || imgEl.getAttribute('data-src') || '') : '';
            
            if (name && priceStr && parseInt(priceStr) > 1000 && !seen.has(name)) {
                seen.add(name);
                products.push({ name, price: parseInt(priceStr), image_url: img, link: href.split('?')[0] });
            }
        });
    }
    
    return products;
}
"""


def scrape_lazada(keyword: str, debug: bool = False) -> list:
    """Scrape produk dari Lazada."""
    print(f"\n{'='*60}")
    print(f"  SCRAPER LAZADA — Keyword: '{keyword}'")
    print(f"{'='*60}")

    products = []

    with sync_playwright() as pw:
        browser = pw.chromium.launch(
            headless=False,
            args=['--disable-blink-features=AutomationControlled', '--no-sandbox']
        )
        context = browser.new_context(
            user_agent=USER_AGENT,
            viewport={'width': 1366, 'height': 768},
            locale='id-ID'
        )
        context.add_init_script(
            "Object.defineProperty(navigator, 'webdriver', { get: () => undefined })"
        )
        page = context.new_page()

        # Buka homepage dulu untuk cookie
        print("\n[Lazada] Mengunjungi homepage untuk cookie...")
        try:
            page.goto("https://www.lazada.co.id/", timeout=30000)
            page.wait_for_timeout(3000)
        except:
            pass

        url = f"https://www.lazada.co.id/catalog/?q={keyword.replace(' ', '+')}"
        print(f"[Lazada] URL: {url}")

        try:
            page.goto(url, timeout=60000)
            page.wait_for_timeout(5000)

            if debug:
                debug_dir = os.path.join(os.path.dirname(__file__), 'debug_screenshots')
                os.makedirs(debug_dir, exist_ok=True)
                page.screenshot(path=os.path.join(debug_dir, "lazada_initial.png"))

            # Scroll sangat perlahan untuk Lazada (lazy-loading lambat)
            for i in range(12):
                page.mouse.wheel(0, 400)
                page.wait_for_timeout(random.uniform(1000, 2000))

            # Scroll balik ke atas lalu ke bawah lagi (trik load ulang)
            page.evaluate("window.scrollTo(0, 0)")
            page.wait_for_timeout(2000)
            for i in range(8):
                page.mouse.wheel(0, 600)
                page.wait_for_timeout(1500)

            if debug:
                page.screenshot(path=os.path.join(debug_dir, "lazada_after_scroll.png"))

            # Extract
            raw = page.evaluate(EXTRACT_JS)
            print(f"  [+] {len(raw)} produk ditemukan dari halaman")

            for p in raw:
                if debug:
                    print(f"    -> {p['name'][:50]} | Rp {p['price']:,}")
                if is_valid_product(p['name'], keyword):
                    products.append({
                        'name': p['name'],
                        'price': p['price'],
                        'image_url': p.get('image_url', ''),
                        'link': p.get('link', ''),
                        'category': 'Elektronik'
                    })

            print(f"[Lazada] {len(products)} produk relevan.")

        except Exception as e:
            print(f"[Lazada] Error: {e}")
            if debug:
                import traceback
                traceback.print_exc()
        finally:
            context.close()
            browser.close()

    return products


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print('Penggunaan: python scraper_lazada.py "kata kunci"')
        print('            python scraper_lazada.py "kata kunci" --debug')
        sys.exit(1)

    KEYWORD = sys.argv[1]
    DEBUG = '--debug' in sys.argv

    products = scrape_lazada(KEYWORD, debug=DEBUG)
    save_to_mysql(products, "Lazada")
    print("\nSelesai!")
