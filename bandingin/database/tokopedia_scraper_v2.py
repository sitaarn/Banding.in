"""
Tokopedia Product Scraper - Playwright Version (v2)
====================================================
Versi yang lebih tahan perubahan UI: parsing berbasis pola URL + heuristik teks,
tidak mengandalkan data-testid kartu produk yang sering berubah.

Setup:
    pip install playwright pandas
    playwright install chromium

Untuk keperluan edukasi dan riset.
"""

import time
import random
import json
from datetime import datetime
from urllib.parse import quote_plus

import pandas as pd
from playwright.sync_api import sync_playwright, TimeoutError as PWTimeout


class TokopediaScraper:
    """Scraper Tokopedia berbasis Playwright dengan parsing URL + heuristik teks."""

    BASE_URL = "https://www.tokopedia.com/search?st=product&q={}"

    USER_AGENT = (
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/120.0.0.0 Safari/537.36"
    )

    # JavaScript untuk extract produk dari halaman search Tokopedia.
    # Strategi: cari semua <a> dalam container produk, filter URL yang
    # benar-benar produk, lalu parse teks tiap kartu secara heuristik.
    EXTRACT_JS = r"""
    () => {
        const container = document.querySelector("[data-testid='divSRPContentProducts']");
        if (!container) return { error: 'container_not_found', products: [] };

        const anchors = container.querySelectorAll("a[href*='tokopedia.com/']");
        const products = [];
        const seen = new Set();

        // Path Tokopedia yang BUKAN produk (perlu di-skip)
        const reservedPaths = new Set([
            'discovery', 'search', 'help', 'about', 'cart', 'login',
            'register', 'category', 'p', 'find', 'promo', 'edu',
            'official-store', 'play', 'feed', 'topads', 'rewards',
            'top-up', 'tagihan', 'tiket', 'wishlist'
        ]);

        anchors.forEach(a => {
            const href = a.href;
            // Pola URL produk: tokopedia.com/{namatoko}/{product-slug}
            const m = href.match(/tokopedia\.com\/([^/?#]+)\/([^/?#]+)/);
            if (!m) return;

            const shop = decodeURIComponent(m[1]);
            const slug = decodeURIComponent(m[2]);
            if (reservedPaths.has(shop)) return;
            if (slug.length < 5) return;  // Slug terlalu pendek -> bukan produk

            const key = shop + '/' + slug;
            if (seen.has(key)) return;
            seen.add(key);

            // Walk up untuk cari card container (ada nama toko + lokasi biasanya
            // di luar tag <a>, jadi perlu ke parent yang lebih besar)
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
            let shopName = null, location = null;
            let discount = null, originalPrice = null;

            for (const line of lines) {
                // Harga: "Rp1.234.567" atau "Rp1,234,567"
                if (!price && /^Rp[\d.,]+$/.test(line)) {
                    price = line;
                }
                // Rating: angka 1-5 dengan optional desimal
                else if (!rating && /^[1-5]([.,][0-9])?$/.test(line)) {
                    rating = line;
                }
                // Terjual: "10+ terjual", "1rb+ terjual", "Terjual 100"
                else if (!sold && /terjual/i.test(line)) {
                    sold = line;
                }
                // Diskon: "30%" atau "-30%"
                else if (!discount && /^-?\d{1,2}%$/.test(line)) {
                    discount = line;
                }
                // Nama produk: baris panjang yang bukan harga/rating/dll
                else if (!name && line.length > 8 && !/^Rp|terjual|^\d+%/i.test(line)) {
                    name = line;
                }
            }

            // 2 baris terakhir biasanya nama toko + lokasi
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

            products.push({
                nama_produk: name,
                harga: price,
                diskon: discount,
                rating: rating,
                terjual: sold,
                toko: shopName || shop,  // fallback ke nama toko dari URL
                lokasi: location,
                link: href.split('?')[0]  // strip query params
            });
        });

        return { products, total_anchors: anchors.length };
    }
    """

    def __init__(self, headless: bool = False):
        self._pw = sync_playwright().start()
        self.browser = self._pw.chromium.launch(
            headless=headless,
            args=[
                "--disable-blink-features=AutomationControlled",
                "--no-sandbox",
            ],
        )
        self.context = self.browser.new_context(
            viewport={"width": 1920, "height": 1080},
            user_agent=self.USER_AGENT,
            locale="id-ID",
            timezone_id="Asia/Jakarta",
        )
        self.context.add_init_script(
            "Object.defineProperty(navigator, 'webdriver', { get: () => undefined })"
        )
        self.page = self.context.new_page()

    def _scroll_page(self, scrolls: int = 8, delay_range: tuple = (1.2, 2.5)):
        """Scroll bertahap untuk memicu lazy-loading produk."""
        for _ in range(scrolls):
            self.page.evaluate("window.scrollBy(0, 800)")
            time.sleep(random.uniform(*delay_range))
        self.page.evaluate("window.scrollTo(0, 0)")
        time.sleep(1)

    def search_products(self, keyword: str, max_pages: int = 3) -> list[dict]:
        all_products = []
        seen_links = set()  # dedup antar halaman

        for page_num in range(1, max_pages + 1):
            url = self.BASE_URL.format(quote_plus(keyword))
            if page_num > 1:
                url += f"&page={page_num}"

            print(f"\n[*] Halaman {page_num}: {url}")

            try:
                self.page.goto(url, wait_until="domcontentloaded", timeout=30000)
            except PWTimeout:
                print(f"  [!] Timeout halaman {page_num}, lewati...")
                continue

            # Tunggu container produk muncul
            try:
                self.page.wait_for_selector(
                    "[data-testid='divSRPContentProducts']", timeout=15000
                )
            except PWTimeout:
                print(f"  [!] Container produk tidak muncul.")
                break

            # Trigger lazy-loading
            self._scroll_page(scrolls=8)

            # Extract via JavaScript
            result = self.page.evaluate(self.EXTRACT_JS)
            if isinstance(result, dict) and result.get("error"):
                print(f"  [!] Error: {result['error']}")
                continue

            products = result.get("products", [])
            total_anchors = result.get("total_anchors", 0)
            print(f"  [+] {total_anchors} link diperiksa, {len(products)} produk valid")

            # Dedup antar halaman
            new_products = []
            for p in products:
                if p["link"] not in seen_links:
                    seen_links.add(p["link"])
                    p["scraped_at"] = datetime.now().isoformat()
                    new_products.append(p)

            print(f"  [+] {len(new_products)} produk baru ditambahkan")
            all_products.extend(new_products)

            # Delay sopan antar halaman
            delay = random.uniform(3.0, 6.0)
            print(f"  [~] Delay {delay:.1f}s...")
            time.sleep(delay)

        return all_products

    def save_to_csv(self, products: list[dict], filename: str):
        if not products:
            print("[!] Tidak ada data untuk disimpan.")
            return
        pd.DataFrame(products).to_csv(filename, index=False, encoding="utf-8-sig")
        print(f"[✓] {len(products)} produk → {filename}")

    def save_to_json(self, products: list[dict], filename: str):
        with open(filename, "w", encoding="utf-8") as f:
            json.dump(products, f, ensure_ascii=False, indent=2)
        print(f"[✓] {len(products)} produk → {filename}")

    def close(self):
        try:
            self.context.close()
            self.browser.close()
            self._pw.stop()
        except Exception:
            pass


def main():
    # ===== Konfigurasi =====
    KEYWORD = "ps5"
    MAX_PAGES = 2
    HEADLESS = False

    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    safe_kw = KEYWORD.replace(" ", "_")
    output_csv = f"tokopedia_{safe_kw}_{timestamp}.csv"
    output_json = f"tokopedia_{safe_kw}_{timestamp}.json"

    scraper = TokopediaScraper(headless=HEADLESS)
    try:
        print(f"[*] Mulai scraping: '{KEYWORD}'")
        products = scraper.search_products(KEYWORD, max_pages=MAX_PAGES)

        print(f"\n[✓] Total produk: {len(products)}")

        if products:
            scraper.save_to_csv(products, output_csv)
            scraper.save_to_json(products, output_json)

            print("\n[*] Sample 5 produk pertama:")
            for i, p in enumerate(products[:5], 1):
                print(f"\n  {i}. {p.get('nama_produk')}")
                print(f"     Harga    : {p.get('harga')}  {p.get('diskon') or ''}")
                print(f"     Toko     : {p.get('toko')}")
                print(f"     Lokasi   : {p.get('lokasi')}")
                print(f"     Rating   : {p.get('rating')} | {p.get('terjual')}")
                print(f"     Link     : {p.get('link')}")
    finally:
        scraper.close()


if __name__ == "__main__":
    main()
