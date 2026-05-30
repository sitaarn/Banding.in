"""
Scraper Tokopedia — Playwright Version
=======================================
Berdasarkan referensi scraper yang sudah terbukti berhasil.
Strategi: parsing URL + heuristik teks (tidak mengandalkan data-testid).

Cara Pakai:
  python scraper_tokopedia.py "iphone 15"
  python scraper_tokopedia.py "ps5 slim" --pages 3
"""
import sys
import os
import time
import random
from urllib.parse import quote_plus

# Pastikan db_helper bisa ditemukan meskipun working directory berbeda
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from playwright.sync_api import sync_playwright, TimeoutError as PWTimeout
from db_helper import is_valid_product, parse_price, save_to_mysql, update_scraper_log



EXTRACT_JS = r"""
() => {
    const container = document.querySelector("[data-testid='divSRPContentProducts']");
    if (!container) return { error: 'container_not_found', products: [] };

    const anchors = container.querySelectorAll("a[href*='tokopedia.com/']");
    const products = [];
    const seen = new Set();

    const reservedPaths = new Set([
        'discovery', 'search', 'help', 'about', 'cart', 'login',
        'register', 'category', 'p', 'find', 'promo', 'edu',
        'official-store', 'play', 'feed', 'topads', 'rewards',
        'top-up', 'tagihan', 'tiket', 'wishlist'
    ]);

    anchors.forEach(a => {
        const href = a.href;
        const m = href.match(/tokopedia\.com\/([^/?#]+)\/([^/?#]+)/);
        if (!m) return;

        const shop = decodeURIComponent(m[1]);
        const slug = decodeURIComponent(m[2]);
        if (reservedPaths.has(shop)) return;
        if (slug.length < 5) return;

        const key = shop + '/' + slug;
        if (seen.has(key)) return;
        seen.add(key);

        let card = a;
        for (let i = 0; i < 6; i++) {
            if (card.parentElement) {
                card = card.parentElement;
                const txt = card.innerText || '';
                if (txt.split('\n').length >= 4) break;
            }
        }

        const text = card.innerText || '';
        const lines = text.split('\n').map(l => l.trim()).filter(Boolean);

        let name = null, price = null, rating = null, sold = null;
        let shopName = null, location = null, discount = null;

        for (const line of lines) {
            if (!price && /^Rp[\d.,]+$/.test(line)) {
                price = line;
            } else if (!rating && /^[1-5]([.,][0-9])?$/.test(line)) {
                rating = line;
            } else if (!sold && /terjual/i.test(line)) {
                sold = line;
            } else if (!discount && /^-?\d{1,2}%$/.test(line)) {
                discount = line;
            } else if (!name && line.length > 8 && !/^Rp|terjual|^\d+%/i.test(line)) {
                name = line;
            }
        }

        if (lines.length >= 2) {
            const last = lines[lines.length - 1];
            const secondLast = lines[lines.length - 2];
            if (last.length < 40 && !/Rp|terjual|^\d+([.,]\d)?$|%$/i.test(last)) {
                location = last;
            }
            if (secondLast.length < 60 && !/Rp|terjual|^\d+([.,]\d)?$|%$/i.test(secondLast)
                && secondLast !== name) {
                shopName = secondLast;
            }
        }

        // Cari gambar
        let imgEl = card.querySelector('img');
        let img = imgEl ? (imgEl.src || imgEl.getAttribute('data-src') || '') : '';
        if (img.includes('data:image')) img = imgEl ? (imgEl.getAttribute('data-src') || '') : '';

        if (name && price) {
            products.push({
                name: name,
                price: price,
                image_url: img,
                link: href.split('?')[0],
                shop: shopName || shop,
                location: location,
                rating: rating,
                sold: sold
            });
        }
    });

    return { products, total_anchors: anchors.length };
}
"""

USER_AGENTS = [
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:133.0) Gecko/20100101 Firefox/133.0",
]


def scrape_tokopedia(keyword: str, max_pages: int = 2) -> list:
    """Scrape produk dari Tokopedia."""
    print(f"\n{'='*60}")
    print(f"  SCRAPER TOKOPEDIA — Keyword: '{keyword}'")
    print(f"{'='*60}")

    all_products = []
    seen_links = set()

    with sync_playwright() as pw:
        # headless=False agar browser terbuka visible (seperti Chrome biasa, tidak di-blokir)
        browser = pw.chromium.launch(
            headless=False,
            args=["--disable-blink-features=AutomationControlled", "--no-sandbox"]
        )
        context = browser.new_context(
            viewport={"width": 1920, "height": 1080},
            locale="id-ID",
            timezone_id="Asia/Jakarta"
        )
        context.add_init_script(
            "Object.defineProperty(navigator, 'webdriver', { get: () => undefined })"
        )
        page = context.new_page()

        for page_num in range(1, max_pages + 1):
            url = f"https://www.tokopedia.com/search?st=product&q={quote_plus(keyword)}&pmin=1000000&ob=2"
            if page_num > 1:
                url += f"&page={page_num}"

            print(f"\n[Tokopedia] Halaman {page_num}: {url}")

            try:
                page.goto(url, wait_until="domcontentloaded", timeout=30000)
            except PWTimeout:
                print(f"  [!] Timeout halaman {page_num}, lewati...")
                continue
            except Exception as e:
                print(f"  [!] Error navigasi: {e}")
                continue

            # Tunggu container produk
            try:
                page.wait_for_selector("[data-testid='divSRPContentProducts']", timeout=15000)
            except PWTimeout:
                print(f"  [!] Container produk tidak muncul di halaman {page_num}.")
                break

            # Scroll untuk lazy-loading
            for _ in range(8):
                page.evaluate("window.scrollBy(0, 800)")
                time.sleep(random.uniform(1.2, 2.5))
            page.evaluate("window.scrollTo(0, 0)")
            time.sleep(1)

            # Extract via JavaScript
            result = page.evaluate(EXTRACT_JS)
            if isinstance(result, dict) and result.get("error"):
                print(f"  [!] Error: {result['error']}")
                continue

            raw_products = result.get("products", [])
            total_anchors = result.get("total_anchors", 0)
            print(f"  [+] {total_anchors} link diperiksa, {len(raw_products)} produk valid")

            # Filter & dedup
            new_count = 0
            for p in raw_products:
                link = p["link"]
                name = p["name"]
                price = parse_price(p["price"])

                if link in seen_links:
                    continue
                if price <= 0:
                    continue
                if not is_valid_product(name, keyword):
                    continue

                seen_links.add(link)
                all_products.append({
                    'name': name,
                    'price': price,
                    'image_url': p.get('image_url', ''),
                    'link': link,
                    'category': 'Elektronik'
                })
                new_count += 1

            print(f"  [+] {new_count} produk relevan ditambahkan")

            # Delay sopan antar halaman
            if page_num < max_pages:
                delay = random.uniform(3.0, 6.0)
                print(f"  [~] Delay {delay:.1f}s...")
                time.sleep(delay)

        context.close()
        browser.close()

    print(f"\n[Tokopedia] TOTAL: {len(all_products)} produk relevan.")
    return all_products


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print('Penggunaan: python scraper_tokopedia.py "kata kunci"')
        print('            python scraper_tokopedia.py "kata kunci" [log_id]')
        sys.exit(1)

    KEYWORD = sys.argv[1]
    LOG_ID = sys.argv[2] if len(sys.argv) > 2 and sys.argv[2].isdigit() else None
    
    MAX_PAGES = 2
    if "--pages" in sys.argv:
        idx = sys.argv.index("--pages")
        if idx + 1 < len(sys.argv):
            MAX_PAGES = int(sys.argv[idx + 1])

    try:
        products = scrape_tokopedia(KEYWORD, max_pages=MAX_PAGES)
        save_to_mysql(products, "Tokopedia")
        if LOG_ID:
            update_scraper_log(LOG_ID, 'success', len(products))
        print("\nSelesai!")
    except Exception as e:
        print(f"\n[!] Error: {e}")
        if LOG_ID:
            update_scraper_log(LOG_ID, 'failed', 0, str(e))
        sys.exit(1)
