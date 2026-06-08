/**
 * ============================================
 * favorit.js - Logika Halaman Favorit
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani:
 * - Load favorit user dari database via AJAX
 * - Render daftar favorit dengan filter platform & sorting
 * - Hapus satu / semua favorit
 * - Statistik (total tersimpan, termurah)
 * - UI: toast, dropdown, avatar
 */

// ── Konstanta Platform ──
const PF_COLORS = { tokopedia: '#42b549', lazada: '#0f146b', blibli: '#0095d9' };
const PF_LABELS = { tokopedia: 'Tokopedia', lazada: 'Lazada', blibli: 'Blibli' };

// Platform yang sedang aktif di filter
let favActivePf = ['tokopedia', 'lazada', 'blibli'];

// ── Helper Functions ──

/** Escape karakter HTML untuk cegah XSS */
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

/** Format harga jadi singkatan. Contoh: 1500000 → "Rp 1.5jt", 50000 → "Rp 50rb" */
function formatPrice(v) {
    if (!v || v === 0) return 'Rp 0';
    if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(v % 1000000 === 0 ? 0 : 1) + 'jt';
    return 'Rp ' + (v / 1000).toFixed(0) + 'rb';
}

/** Tampilkan toast notification (hijau = sukses, merah = error) */
function showToast(msg, isError = false) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.style.background = isError ? '#dc3545' : '#2a9d8f';
    t.classList.add('visible');
    setTimeout(() => t.classList.remove('visible'), 2200);
}

// ── CRUD Favorit ──

/**
 * Load data favorit user dari database via API /favorites.
 * Juga cache di localStorage sebagai fallback.
 */
async function loadFavoritesFromDB() {
    try {
        const res = await fetch('/bandingin/favorites');
        const result = await res.json();
        if (result.success && result.data) {
            const favorites = result.data.map(item => ({
                id: item.product_id,
                name: item.product_name,
                price: item.price,
                platform: item.platform,
                link: item.product_link || '#',
                emoji: '📦',
                sub: item.category || '',
                timestamp: new Date(item.created_at).getTime()
            }));
            localStorage.setItem('userFavorites', JSON.stringify(favorites));
            return favorites;
        }
        return [];
    } catch (error) {
        console.error('Gagal load favorit:', error);
        return JSON.parse(localStorage.getItem('userFavorites') || '[]');
    }
}

/** Hapus satu favorit dari database via API toggle */
async function removeFromDatabase(productId, platform, productName) {
    try {
        const res = await fetch('/bandingin/favorit/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, platform: platform })
        });
        const result = await res.json();
        if (result.success) {
            showToast(`✗ ${productName} dihapus dari favorit`);
            return true;
        } else {
            showToast(result.error || 'Gagal menghapus', true);
            return false;
        }
    } catch (error) {
        console.error('Error hapus:', error);
        showToast('Gagal menghapus', true);
        return false;
    }
}

/** Hapus satu favorit dan re-render list */
async function favHapusSatu(productId, platform, productName) {
    if (await removeFromDatabase(productId, platform, productName)) {
        await favRender();
    }
}

/** Hapus SEMUA favorit (dengan konfirmasi) dan re-render list */
async function favHapusSemua() {
    const favs = await loadFavoritesFromDB();
    if (!favs.length) return;
    if (!confirm('Hapus semua favorit?')) return;
    let berhasil = 0;
    for (const fav of favs) {
        const res = await fetch('/bandingin/favorit/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: fav.id, platform: fav.platform })
        });
        const result = await res.json();
        if (result.success) berhasil++;
    }
    showToast(`🗑️ ${berhasil} favorit dihapus`);
    await favRender();
}

/** Buka link produk di tab baru */
function favKunjungi(link) {
    if (link && link !== '#') {
        window.open(link, '_blank');
    } else {
        showToast('Link produk tidak tersedia', true);
    }
}

// ── Filter Platform ──

/** Toggle filter platform di sidebar. 'semua' = aktifkan semua. */
function favTogglePf(btn) {
    const pf = btn.dataset.pf;
    if (pf === 'semua') {
        document.querySelectorAll('.fav-pf-btn').forEach(b => b.classList.add('active'));
        favActivePf = ['tokopedia', 'lazada', 'blibli'];
    } else {
        btn.classList.toggle('active');
        const allPfBtns = [...document.querySelectorAll('.fav-pf-btn[data-pf!="semua"]')];
        favActivePf = allPfBtns.filter(b => b.classList.contains('active')).map(b => b.dataset.pf);
        // Update tombol "Semua"
        const semuaBtn = document.querySelector('.fav-pf-btn[data-pf="semua"]');
        const allActive = allPfBtns.every(b => b.classList.contains('active'));
        if (semuaBtn) semuaBtn.classList.toggle('active', allActive);
    }
    favRender();
}

// ── Render Daftar Favorit ──

/**
 * Render ulang seluruh daftar favorit.
 * Ambil data dari DB, filter platform, sort, hitung statistik, generate HTML.
 */
async function favRender() {
    const listEl = document.getElementById('favList');
    if (!listEl) return;

    let favs = await loadFavoritesFromDB();
    const sortSelect = document.getElementById('favSortSelect');
    const sort = sortSelect ? sortSelect.value : 'newest';

    // Filter berdasarkan platform aktif
    if (favActivePf.length < 4) {
        favs = favs.filter(f => favActivePf.includes((f.platform || '').toLowerCase()));
    }

    // Sorting
    if (sort === 'newest') favs.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));
    if (sort === 'cheapest') favs.sort((a, b) => (a.price || 0) - (b.price || 0));
    if (sort === 'expensive') favs.sort((a, b) => (b.price || 0) - (a.price || 0));
    if (sort === 'az') favs.sort((a, b) => a.name.localeCompare(b.name));

    // Update statistik sidebar
    const allFavs = await loadFavoritesFromDB();
    document.getElementById('statTersimpan').textContent = allFavs.length;
    if (allFavs.length > 0) {
        const minPrice = Math.min(...allFavs.map(f => f.price || 0));
        document.getElementById('statTermurah').textContent = allFavs.filter(f => f.price === minPrice).length;
    } else {
        document.getElementById('statTermurah').textContent = 0;
    }

    // Tampilkan empty state jika tidak ada favorit
    if (!favs.length) {
        listEl.innerHTML = `<div class="fav-empty"><div class="fav-empty-icon"><i class="fa-solid fa-heart text-red"></i></div><div class="fav-empty-title">Belum Ada Favorit</div><div class="fav-empty-sub">${allFavs.length && favActivePf.length < 4 ? 'Tidak ada favorit di platform yang dipilih.<br>Coba ubah filter platform.' : 'Mulai bandingkan harga dan simpan produk yang kamu suka ke sini.'}</div><button class="fav-empty-btn" onclick="window.location.href='/bandingin/list'">Cari Produk →</button></div>`;
        return;
    }

    // Cari harga termurah untuk badge
    const allPrices = favs.map(f => f.price || 0).filter(p => p > 0);
    const globalMin = allPrices.length ? Math.min(...allPrices) : 0;

    // Render setiap item favorit
    listEl.innerHTML = favs.map((fav, idx) => {
        const isCheapest = fav.price === globalMin && globalMin > 0;
        const pfColor = PF_COLORS[fav.platform.toLowerCase()] || '#888';
        const pfLabel = PF_LABELS[fav.platform.toLowerCase()] || fav.platform;
        return `
            <div class="fav-item">
                <div class="fav-item-rank ${idx === 0 ? 'gold' : ''}">${idx+1}</div>
                <div class="fav-item-body">
                    <div class="fav-item-name">${escapeHtml(fav.name)}</div>
                    <div class="fav-item-sub">${escapeHtml(fav.sub || '')}</div>
                    <div class="fav-item-platform">
                        <div class="fav-item-pf-dot" style="background:${pfColor}"></div>
                        ${pfLabel}
                    </div>
                    <div class="fav-item-price ${isCheapest ? 'best' : ''}">${formatPrice(fav.price)}</div>
                </div>
                <div class="fav-item-actions">
                    <button class="fav-btn-hapus" onclick="favHapusSatu(${fav.id}, '${fav.platform}', '${escapeHtml(fav.name).replace(/'/g, "\\'")}')">Hapus</button>
                    <button class="fav-btn-cari" onclick="favKunjungi('${fav.link || '#'}')">Kunjungi →</button>
                </div>
            </div>
        `;
    }).join('');
}

// ── UI: Dropdown, Logout, Avatar ──

/** Toggle dropdown user */
function toggleDropdown() { document.getElementById('userChipWrap')?.classList.toggle('open'); }

/** Tutup dropdown saat klik di luar */
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('userChipWrap');
    if (wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
});

/** Logout dan redirect */
function doLogout() {
    localStorage.removeItem('loggedIn');
    window.location.href = '/bandingin/logout';
}

/** Inisialisasi avatar user dari atribut data-avatar */
function initAvatar() {
    const el = document.getElementById('userAvatar');
    if (!el) return;
    const name = el.dataset.avatar || '';
    const parts = name.trim().split(' ');
    el.textContent = parts.length >= 2 ? (parts[0][0] + parts[1][0]).toUpperCase() : name.substring(0,2).toUpperCase();
}

/** Saat DOM ready: init avatar dan render favorit */
window.addEventListener('DOMContentLoaded', async function() {
    initAvatar();
    await favRender();
});