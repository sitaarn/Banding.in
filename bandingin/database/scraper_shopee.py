"""
Scraper Shopee — Selenium UC (Login Manual)
============================================
Shopee memblokir akses pencarian tanpa login, baik via browser maupun API.
Solusi: Buka browser dengan undetected-chromedriver, login MANUAL,
lalu baru jalankan scraping.

Cara Pakai:
  python scraper_shopee.py "iphone 15"

Alur:
  1. Browser Chrome terbuka otomatis ke halaman login Shopee
  2. Anda LOGIN secara manual (bisa pakai QR code / SMS / password)
  3. Setelah login berhasil, tekan ENTER di terminal
  4. Script akan otomatis mencari dan mengambil data produk
"""
import sys
import time
import re
import random

from db_helper import is_valid_product, parse_price, save_to_mysql

# Coba import selenium UC, fallback ke Playwright jika tidak ada
try:
    import undetected_chromedriver as uc
    from selenium.webdriver.common.by import By
    HAS_UC = True
except ImportError:
    HAS_UC = False

try:
    from playwright.sync_api import sync_playwright
    HAS_PLAYWRIGHT = True
except ImportError:
    HAS_PLAYWRIGHT = False


EXTRACT_JS = """
var products = [];
var links = document.querySelectorAll('a');
for(var i=0; i<links.length; i++) {
    var text = links[i].innerText;
    var img = links[i].querySelector('img');
    if(img && text.includes('Rp')) {
        var lines = text.split('\\n');
        var name = '';
        var price = '';
        for(var j=0; j<lines.length; j++) {
            var line = lines[j].trim();
            if(!price && /Rp[\\d.,]+/.test(line)) {
                price = line.replace(/[^0-9]/g, '');
            } else if(!name && line.length > 10 && !/^Rp|terjual|^\\d+%|^Ad$/i.test(line)) {
                name = line;
            }
        }
        if(name && price && parseInt(price) > 10000) {
            products.push({
                name: name,
                price: parseInt(price),
                link: links[i].href.split('?')[0],
                img: img.src || ''
            });
        }
    }
}
return products;
"""


def scrape_shopee_uc(keyword: str) -> list:
    """Scrape Shopee menggunakan undetected-chromedriver dengan login manual."""
    print(f"\n{'='*60}")
    print(f"  SCRAPER SHOPEE (UC) — Keyword: '{keyword}'")
    print(f"{'='*60}")

    if not HAS_UC:
        print("[!] undetected-chromedriver belum terinstall.")
        print("    Jalankan: pip install undetected-chromedriver")
        return []

    options = uc.ChromeOptions()
    driver = uc.Chrome(options=options)

    try:
        # Step 1: Buka halaman login Shopee
        print("\n[Shopee] Membuka halaman login Shopee...")
        driver.get("https://shopee.co.id/buyer/login")
        
        print("\n" + "="*50)
        print("  SILAKAN LOGIN DI BROWSER YANG TERBUKA!")
        print("  (Gunakan QR Code, SMS, atau Password)")
        print("  Setelah login berhasil, tekan ENTER di sini...")
        print("="*50)
        input("\n>>> Tekan ENTER setelah login berhasil: ")
        
        # Step 2: Verifikasi login
        time.sleep(2)
        print("\n[Shopee] Memverifikasi login...")
        driver.get("https://shopee.co.id/")
        time.sleep(3)
        
        # Step 3: Cari produk
        search_url = f"https://shopee.co.id/search?keyword={keyword.replace(' ', '+')}"
        print(f"[Shopee] Mencari: {search_url}")
        driver.get(search_url)
        time.sleep(5)
        
        # Cek apakah ada popup bahasa
        try:
            lang_btns = driver.find_elements(By.XPATH, "//*[contains(text(), 'Bahasa Indonesia')]")
            if lang_btns:
                lang_btns[0].click()
                time.sleep(3)
        except:
            pass

        # Scroll untuk memuat produk
        print("[Shopee] Scrolling untuk memuat produk...")
        for _ in range(10):
            driver.execute_script("window.scrollBy(0, 600);")
            time.sleep(random.uniform(1.0, 2.0))

        # Extract produk via JS
        raw = driver.execute_script(EXTRACT_JS)
        print(f"  [+] {len(raw)} produk ditemukan dari halaman")

        products = []
        for p in raw:
            name = p.get('name', '')
            price = p.get('price', 0)
            
            if name and price > 0 and is_valid_product(name, keyword):
                products.append({
                    'name': name,
                    'price': price,
                    'image_url': p.get('img', ''),
                    'link': p.get('link', ''),
                    'category': 'Elektronik'
                })

        print(f"\n[Shopee] TOTAL: {len(products)} produk relevan.")
        return products

    except Exception as e:
        print(f"[Shopee] Error: {e}")
        import traceback
        traceback.print_exc()
        return []
    finally:
        try:
            driver.quit()
        except:
            pass


def scrape_shopee_playwright(keyword: str) -> list:
    """Fallback: Scrape Shopee via Playwright (tanpa login, mungkin terbatas)."""
    print(f"\n{'='*60}")
    print(f"  SCRAPER SHOPEE (Playwright) — Keyword: '{keyword}'")
    print(f"{'='*60}")

    if not HAS_PLAYWRIGHT:
        print("[!] Playwright belum terinstall.")
        return []

    products = []
    with sync_playwright() as pw:
        browser = pw.chromium.launch(headless=False)
        context = browser.new_context(
            user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
            locale="id-ID"
        )
        page = context.new_page()

        print("\n[Shopee] Membuka halaman login...")
        page.goto("https://shopee.co.id/buyer/login")
        
        print("\n" + "="*50)
        print("  SILAKAN LOGIN DI BROWSER YANG TERBUKA!")
        print("  Setelah login berhasil, tekan ENTER di sini...")
        print("="*50)
        input("\n>>> Tekan ENTER setelah login berhasil: ")

        time.sleep(2)
        search_url = f"https://shopee.co.id/search?keyword={keyword.replace(' ', '+')}"
        print(f"[Shopee] Mencari: {search_url}")
        page.goto(search_url)
        page.wait_for_timeout(5000)

        for _ in range(10):
            page.mouse.wheel(0, 600)
            page.wait_for_timeout(1500)

        raw = page.evaluate(EXTRACT_JS)
        print(f"  [+] {len(raw)} produk ditemukan")

        for p in raw:
            if is_valid_product(p['name'], keyword):
                products.append({
                    'name': p['name'],
                    'price': p['price'],
                    'image_url': p.get('img', ''),
                    'link': p.get('link', ''),
                    'category': 'Elektronik'
                })

        print(f"\n[Shopee] TOTAL: {len(products)} produk relevan.")
        context.close()
        browser.close()

    return products


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print('Penggunaan: python scraper_shopee.py "kata kunci"')
        sys.exit(1)

    KEYWORD = sys.argv[1]

    # Gunakan UC jika tersedia, fallback ke Playwright
    if HAS_UC:
        products = scrape_shopee_uc(KEYWORD)
    elif HAS_PLAYWRIGHT:
        products = scrape_shopee_playwright(KEYWORD)
    else:
        print("[!] Tidak ada driver browser yang tersedia!")
        print("    Install salah satu: pip install undetected-chromedriver")
        print("    Atau: pip install playwright && playwright install")
        sys.exit(1)

    save_to_mysql(products, "Shopee")
    print("\nSelesai!")
