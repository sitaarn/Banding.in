"""
Scraper Blibli — Playwright Version
=====================================
Scraper yang sudah terbukti berhasil untuk Blibli.
Menggunakan Playwright untuk rendering JavaScript.

Cara Pakai:
  python scraper_blibli.py "iphone 15"
  python scraper_blibli.py "ps5 slim" --debug
"""
import sys
import os
import time
import random

# Pastikan db_helper bisa ditemukan meskipun working directory berbeda
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from playwright.sync_api import sync_playwright

from db_helper import is_valid_product, parse_price, save_to_mysql, update_scraper_log


USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
]

EXTRACT_JS = r"""
() => {
    const products = [];
    const seen = new Set();
    
    // Strategi: Cari semua link produk blibli (/p/)
    const allLinks = document.querySelectorAll('a[href*="/p/"]');
    
    allLinks.forEach(link => {
        // Naik ke parent card
        let card = link;
        for (let i = 0; i < 8; i++) {
            if (!card.parentElement) break;
            card = card.parentElement;
            const text = card.innerText || '';
            if (text.includes('Rp') && text.length > 30 && text.length < 5000) break;
        }
        
        // Ambil nama
        let name = '';
        const nameEl = card.querySelector('[class*="name"], [class*="Name"], [class*="title"]');
        if (nameEl) {
            name = nameEl.textContent.trim();
        } else {
            name = link.title || link.getAttribute('aria-label') || '';
            if (!name) {
                const divs = card.querySelectorAll('div, span');
                for (let d of divs) {
                    let t = d.textContent.trim();
                    if (t.length > 10 && t.length < 200 && !t.includes('Rp') && !t.includes('%')) {
                        name = t;
                        break;
                    }
                }
            }
        }
        
        // Ambil harga
        let priceStr = '';
        const cardText = card.innerText || '';
        const priceMatches = cardText.match(/Rp\s*([\d.]+)/g);
        if (priceMatches) {
            priceStr = priceMatches[0].replace(/[^0-9]/g, '');
        }
        
        let href = link.href.split('?')[0];
        let imgEl = card.querySelector('img');
        let img = imgEl ? (imgEl.src || imgEl.getAttribute('data-src') || '') : '';
        
        if (name && priceStr && parseInt(priceStr) > 1000 && !seen.has(name)) {
            seen.add(name);
            products.push({
                name: name,
                price: parseInt(priceStr),
                image_url: img,
                link: href
            });
        }
    });
    
    return products;
}
"""


def scrape_blibli(keyword: str, debug: bool = False) -> list:
    """Scrape produk dari Blibli."""
    print(f"\n{'='*60}")
    print(f"  SCRAPER BLIBLI — Keyword: '{keyword}'")
    print(f"{'='*60}")

    products = []

    with sync_playwright() as pw:
        # headless=False agar browser terbuka visible (tidak di-blokir anti-bot)
        browser = pw.chromium.launch(
            headless=False,
            args=['--disable-blink-features=AutomationControlled', '--no-sandbox']
        )
        context = browser.new_context(
            viewport={'width': 1366, 'height': 768},
            locale='id-ID'
        )
        context.add_init_script(
            "Object.defineProperty(navigator, 'webdriver', { get: () => undefined })"
        )
        page = context.new_page()

        url = f"https://www.blibli.com/cari/{keyword.replace(' ', '%20')}"
        print(f"\n[Blibli] URL: {url}")

        try:
            page.goto(url, timeout=60000)
            page.wait_for_timeout(5000)

            # Deteksi apakah halaman di-block oleh anti-bot
            page_text = page.evaluate("document.body.innerText || ''")
            if "aktivitas yang tidak biasa" in page_text.lower() or "tidak biasa" in page_text.lower():
                print("  [!] Blibli mendeteksi bot — IP ter-block sementara.")
                print("  [!] Coba lagi nanti atau gunakan koneksi internet berbeda.")
                raise Exception("Blibli anti-bot: IP blocked sementara")

            # Scroll
            page.evaluate("window.scrollTo(0, 0)")
            page.wait_for_timeout(1000)
            for _ in range(6):
                page.mouse.wheel(0, 500)
                page.wait_for_timeout(1000)

            if debug:
                debug_dir = os.path.join(os.path.dirname(__file__), 'debug_screenshots')
                os.makedirs(debug_dir, exist_ok=True)
                page.screenshot(path=os.path.join(debug_dir, "blibli_result.png"))

            # Extract
            raw = page.evaluate(EXTRACT_JS)
            print(f"  [+] {len(raw)} produk ditemukan dari halaman")

            for p in raw:
                if is_valid_product(p['name'], keyword):
                    products.append({
                        'name': p['name'],
                        'price': p['price'],
                        'image_url': p.get('image_url', ''),
                        'link': p.get('link', ''),
                        'category': 'Elektronik'
                    })

            print(f"[Blibli] {len(products)} produk relevan.")

        except Exception as e:
            print(f"[Blibli] Error: {e}")
            if debug:
                import traceback
                traceback.print_exc()
        finally:
            context.close()
            browser.close()

    return products


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print('Penggunaan: python scraper_blibli.py "kata kunci"')
        print('            python scraper_blibli.py "kata kunci" [log_id]')
        sys.exit(1)

    KEYWORD = sys.argv[1]
    LOG_ID = sys.argv[2] if len(sys.argv) > 2 and sys.argv[2].isdigit() else None
    
    DEBUG = '--debug' in sys.argv
    try:
        products = scrape_blibli(KEYWORD, debug=DEBUG)
        save_to_mysql(products, "Blibli")
        if LOG_ID:
            update_scraper_log(LOG_ID, 'success', len(products))
        print("\nSelesai!")
    except Exception as e:
        print(f"\n[!] Error: {e}")
        if LOG_ID:
            update_scraper_log(LOG_ID, 'failed', 0, str(e))
        sys.exit(1)
