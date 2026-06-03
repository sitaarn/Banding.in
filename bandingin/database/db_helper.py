"""
db_helper.py — Modul bantuan database untuk semua scraper Banding.in
====================================================================
Berisi: koneksi MySQL, negative keyword filter, dan fungsi save_to_mysql.
"""
import re
import mysql.connector

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'bandingin'
}

NEGATIVE_KEYWORDS = [
    "case", "casing", "charger", "kabel", "cable", "adaptor", "adapter",
    "cover", "glass", "pelindung", "box", "dus", "stiker", "tempered",
    "softcase", "hardcase", "silikon", "dummy", "strap", "baterai", "battery",
    "hydrogel", "screen protector", "lanyard", "magsafe", "antigores",
    "garskin", "skin", "ring", "holder", "stand", "tripod", "tongsis",
    "earphone", "headset", "powerbank", "power bank", "tali", "mount",
    "bracket", "pouch", "docking", "dock", "stylus", "pen", "film",
    "bumper", "armor", "spigen", "otterbox", "nillkin", "flip cover"
]

def is_valid_product(product_name: str, search_keyword: str) -> bool:
    """Cek apakah nama produk relevan (bukan aksesoris)."""
    name_lower = product_name.lower()
    
    # Cek negative keywords
    for neg in NEGATIVE_KEYWORDS:
        if neg in name_lower:
            return False
    
    # Cek apakah semua kata kunci pencarian ada di nama produk
    keywords = search_keyword.lower().split()
    for kw in keywords:
        if kw not in name_lower:
            return False
    
    return True

def parse_price(price_str: str) -> int:
    """Konversi string harga (Rp12.345.678) ke integer."""
    if not price_str:
        return 0
    cleaned = re.sub(r'[^0-9]', '', str(price_str))
    return int(cleaned) if cleaned else 0

def save_to_mysql(products: list, platform_name: str):
    """Simpan list produk ke database MySQL."""
    if not products:
        print(f"\n[{platform_name}] Tidak ada produk untuk disimpan.")
        return
    
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()

        # Dapatkan atau buat platform
        cursor.execute("SELECT id, LOWER(name) FROM platforms")
        platform_map = {row[1]: row[0] for row in cursor.fetchall()}
        
        plat_key = platform_name.lower()
        if plat_key not in platform_map:
            cursor.execute("INSERT INTO platforms (name) VALUES (%s)", (platform_name,))
            platform_id = cursor.lastrowid
            platform_map[plat_key] = platform_id
            print(f"  [DB] Platform baru ditambahkan: {platform_name} (id={platform_id})")
        else:
            platform_id = platform_map[plat_key]

        inserted = 0
        updated = 0

        for p in products:
            name = p['name']
            price = p['price']
            image = p.get('image_url', '')
            link = p.get('link', '')
            category = p.get('category', 'Elektronik')

            # Cek apakah produk sudah ada
            cursor.execute("SELECT id FROM products WHERE name = %s LIMIT 1", (name,))
            existing = cursor.fetchone()

            if existing:
                product_id = existing[0]
            else:
                cursor.execute(
                    "INSERT INTO products (name, category) VALUES (%s, %s)",
                    (name, category)
                )
                product_id = cursor.lastrowid

            # Update atau insert harga
            cursor.execute(
                "SELECT id FROM product_prices WHERE product_id = %s AND platform_id = %s",
                (product_id, platform_id)
            )
            existing_price = cursor.fetchone()

            if existing_price:
                cursor.execute(
                    "UPDATE product_prices SET price = %s, link = %s WHERE product_id = %s AND platform_id = %s",
                    (price, link, product_id, platform_id)
                )
                updated += 1
            else:
                cursor.execute(
                    "INSERT INTO product_prices (product_id, platform_id, price, link) VALUES (%s, %s, %s, %s)",
                    (product_id, platform_id, price, link)
                )
                inserted += 1

        conn.commit()
        print(f"  [{platform_name} DB] Baru: {inserted} | Update: {updated}")

    except mysql.connector.Error as err:
        print(f"  [DB Error] {err}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()

def update_scraper_log(log_id: int, status: str, items_scraped: int = 0, error_message: str = None):
    """Update status scraper_logs."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor()
        cursor.execute(
            "UPDATE scraper_logs SET status = %s, items_scraped = %s, error_message = %s, finished_at = NOW() WHERE id = %s",
            (status, items_scraped, error_message, log_id)
        )
        conn.commit()
    except mysql.connector.Error as err:
        print(f"  [DB Error] {err}")
    finally:
        if 'conn' in locals() and conn.is_connected():
            cursor.close()
            conn.close()
