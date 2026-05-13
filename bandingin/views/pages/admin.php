

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Banding.in — Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="./public/css/list.css"/>
  <link rel="stylesheet" href="./public/css/admin.css"/>
</head>
<body>

  <!-- Background layers (identik) -->
  <div class="bg"></div>
  <div class="blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>
  <div class="orbs">
    <div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div>
    <div class="orb orb-4"></div><div class="orb orb-5"></div><div class="orb orb-6"></div>
    <div class="orb orb-7"></div>
  </div>
  <div class="grid-overlay"></div>

  <!-- Navigation (identik) -->
  <nav>
    <span class="nav-brand" onclick="window.location.href='http://localhost/bandingin/landing'">
      Banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em>
    </span>
    <div class="nav-links">
      <?php if(isset($_SESSION['name'])) : ?>
        <div class="user-chip-wrap" id="userChipWrap">
          <div class="user-chip" onclick="toggleDropdown()">
            <div class="user-avatar" id="userAvatar" data-avatar="<?= htmlspecialchars($_SESSION['name']) ?>"></div>
            <span class="user-name"><?= htmlspecialchars($_SESSION['name']) ?></span>
            <div class="user-online"></div>
            <span class="user-chevron">▼</span>
          </div>
          <div class="user-dropdown">
            <div class="dropdown-info">
              <div class="dropdown-info-name"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></div>
              <div class="dropdown-info-label">Admin ✓</div>
            </div>
            <a class="dropdown-item" href="<?= (BASE_URL ?? '/') . 'profile' ?>">
              <span class="dropdown-icon">👤</span> Profil Saya
            </a>
            <a class="dropdown-item" href="<?= (BASE_URL ?? '/') . 'list' ?>">
              <span class="dropdown-icon">🔍</span> Ke Halaman Utama
            </a>
            <div class="dropdown-item logout" onclick="doLogout()">
              <span class="dropdown-icon">🚪</span> Logout
            </div>
          </div>
        </div>
      <?php else: ?>
        <button class="nav-btn" onclick="window.location.href='<?= (BASE_URL ?? '/') . 'login' ?>'">Login</button>
      <?php endif; ?>
      <div class="admin-badge">Admin Panel</div>
    </div>
  </nav>

  <!-- ADMIN PAGE -->
  <div class="page" id="adminPage" style="align-items:flex-start; overflow-y:auto; overflow-x:hidden; padding-top:72px;">
    <div class="listing-shell">


      <!-- MAIN AREA -->
      <div class="ls-main" id="lsMain">

        <!-- ── SECTION: TAMBAH PRODUK ── -->
        <div class="adm-section" id="secAdd">
          <div class="ls-topbar">
            <div class="ls-query-info">
              <div class="ls-query-title">Tambah Produk</div>
              <div class="ls-query-meta">Isi semua field untuk menambahkan produk baru ke database</div>
            </div>
          </div>

          <div class="adm-form-card">

            <div class="adm-form-grid">

              <!-- Nama Produk -->
              <div class="adm-field adm-field-full">
                <label class="adm-label">Nama Produk <span class="adm-required">*</span></label>
                <input class="adm-input" type="text" id="fName" name="name" placeholder="cth. Samsung Galaxy S24 Ultra 256GB" autocomplete="off"/>
              </div>

              <!-- Platform -->
              <div class="adm-field">
                <label class="adm-label">Platform <span class="adm-required">*</span></label>
                <div class="adm-select-wrap">
                  <select class="adm-input adm-select" id="fPlatform" name="platform">
                    <option value="">— Pilih Platform —</option>
                    <option value="tokopedia">Tokopedia</option>
                    <option value="shopee">Shopee</option>
                    <option value="lazada">Lazada</option>
                    <option value="blibli">Blibli</option>
                  </select>
                  <span class="adm-select-arrow">▾</span>
                </div>
              </div>

              <!-- Harga -->
              <div class="adm-field">
                <label class="adm-label">Harga Jual (Rp) <span class="adm-required">*</span></label>
                <div class="adm-input-prefix-wrap">
                  <span class="adm-prefix">Rp</span>
                  <input class="adm-input adm-input-prefixed" type="number" id="fPrice" name="price" placeholder="0" min="0" step="1000"/>
                  </div>
                </div>
              </div>

              <!-- URL Produk -->
              <div class="adm-field adm-field-full">
                <label class="adm-label">URL Produk <span class="adm-required">*</span></label>
                <div class="adm-input-icon-wrap">
                  <span class="adm-input-icon">🔗</span>
                  <input class="adm-input adm-input-iconed" type="url" id="fUrl" name="url" placeholder="https://www.tokopedia.com/..."/>
                </div>
              </div>

              <!-- URL Gambar -->
              <div class="adm-field adm-field-full">
                <label class="adm-label">URL Gambar Produk</label>
                <div class="adm-input-icon-wrap">
                  <span class="adm-input-icon">🖼️</span>
                  <input class="adm-input adm-input-iconed" type="url" id="fImg" name="img_url" placeholder="https://... (opsional, gunakan emoji jika kosong)"/>
                </div>
              </div>

              <!-- Tag / Kata Kunci Pencarian - DIHAPUS sesuai permintaan -->
              <!-- Deskripsi Singkat - DIHAPUS sesuai permintaan -->

            </div><!-- end grid -->

            <!-- Preview Card - SELURUH BLOK DIHAPUS sesuai permintaan -->

            <!-- Submit -->
            <div class="adm-form-actions">
              <button class="adm-btn-reset" type="button" onclick="resetForm()">↺ Reset Form</button>
              <button class="adm-btn-submit" type="button" onclick="submitProduct()">
                <span id="submitTxt">✓ Simpan Produk</span>
              </button>
            </div>

          </div><!-- end form-card -->
        </div><!-- end secAdd -->


        <!-- ── SECTION: DAFTAR PRODUK ── -->
        <div class="adm-section hidden" id="secList">
          <div class="ls-topbar">
            <div class="ls-query-info">
              <div class="ls-query-title">Daftar Produk</div>
              <div class="ls-query-meta">Kelola semua produk yang telah ditambahkan</div>
            </div>
            <div class="adm-search-wrap">
              <span class="adm-search-icon">🔍</span>
              <input class="adm-search-input" type="text" id="admSearch" placeholder="Cari produk..." oninput="filterProductList()"/>
            </div>
          </div>

          <div id="productListWrap">
            <?php if(!empty($products)): ?>
              <?php foreach($products as $i => $p): ?>
              <div class="ls-card adm-product-row" data-name="<?= strtolower(htmlspecialchars($p['name'])) ?>" data-platform="<?= htmlspecialchars($p['platform']) ?>">
                <div class="ls-card-rank <?= $i === 0 ? 'gold' : '' ?>"><?= $i + 1 ?></div>
                <div class="ls-card-img"><?= htmlspecialchars($p['emoji'] ?? '📦') ?></div>
                <div class="ls-card-body">
                  <div class="ls-card-top">
                    <div>
                      <div class="ls-card-name"><?= htmlspecialchars($p['name']) ?></div>
                      <div class="ls-card-sub"><?= ucfirst(htmlspecialchars($p['platform'])) ?> · <?= htmlspecialchars($p['category'] ?? 'Lainnya') ?></div>
                    </div>
                    <div class="ls-card-badges">
                      <?php if(!empty($p['discount'])): ?>
                        <span class="ls-badge ls-badge-cheap">-<?= $p['discount'] ?>%</span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="ls-platform-tag">
                    <div class="ls-pf-logo" style="background:<?= PLATFORM_COLORS[$p['platform']] ?? '#888' ?>"><?= strtoupper(substr($p['platform'],0,1)) ?></div>
                    <span><?= ucfirst(htmlspecialchars($p['platform'])) ?></span>
                    <?php if(!empty($p['rating'])): ?>
                      <span style="opacity:.4">·</span>
                      <span>★ <?= $p['rating'] ?></span>
                    <?php endif; ?>
                    <?php if(!empty($p['sold'])): ?>
                      <span style="opacity:.4">·</span>
                      <span><?= number_format($p['sold']) ?> terjual</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">
                  <div style="display:flex;align-items:baseline;gap:6px;">
                    <span class="ls-card-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
                    <?php if(!empty($p['orig_price'])): ?>
                      <span class="ls-card-orig">Rp <?= number_format($p['orig_price'], 0, ',', '.') ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="ls-card-actions">
                    <button class="adm-btn-edit ls-btn-save" title="Edit" onclick="editProduct(<?= $p['id'] ?>)">✏️</button>
                    <button class="adm-btn-del ls-btn-save" title="Hapus" onclick="deleteProduct(<?= $p['id'] ?>, this)">🗑️</button>
                    <a class="ls-btn-visit" href="<?= htmlspecialchars($p['url']) ?>" target="_blank">Lihat ↗</a>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="ls-empty">
                <div class="ls-empty-icon">📭</div>
                <div class="ls-empty-text">Belum ada produk. Tambahkan produk pertama kamu!</div>
              </div>
            <?php endif; ?>
          </div>
        </div><!-- end secList -->


        <!-- ── SECTION: STATISTIK ── -->
        <div class="adm-section hidden" id="secStats">
          <div class="ls-topbar">
            <div class="ls-query-info">
              <div class="ls-query-title">Statistik</div>
              <div class="ls-query-meta">Gambaran data produk di database</div>
            </div>
          </div>

          <div class="adm-stats-grid">
            <div class="adm-stat-card">
              <div class="adm-stat-card-icon">📦</div>
              <div class="adm-stat-card-val"><?= $totalProducts ?? 0 ?></div>
              <div class="adm-stat-card-lbl">Total Produk</div>
            </div>
            <div class="adm-stat-card">
              <div class="adm-stat-card-icon" style="background:rgba(66,181,73,.15)">🟢</div>
              <div class="adm-stat-card-val"><?= $countByPlatform['tokopedia'] ?? 0 ?></div>
              <div class="adm-stat-card-lbl">Tokopedia</div>
            </div>
            <div class="adm-stat-card">
              <div class="adm-stat-card-icon" style="background:rgba(238,77,45,.15)">🔴</div>
              <div class="adm-stat-card-val"><?= $countByPlatform['shopee'] ?? 0 ?></div>
              <div class="adm-stat-card-lbl">Shopee</div>
            </div>
            <div class="adm-stat-card">
              <div class="adm-stat-card-icon" style="background:rgba(15,20,107,.12)">🔵</div>
              <div class="adm-stat-card-val"><?= $countByPlatform['lazada'] ?? 0 ?></div>
              <div class="adm-stat-card-lbl">Lazada</div>
            </div>
            <div class="adm-stat-card">
              <div class="adm-stat-card-icon" style="background:rgba(0,149,217,.15)">💙</div>
              <div class="adm-stat-card-val"><?= $countByPlatform['blibli'] ?? 0 ?></div>
              <div class="adm-stat-card-lbl">Blibli</div>
            </div>
            <div class="adm-stat-card">
              <div class="adm-stat-card-icon">💰</div>
              <div class="adm-stat-card-val">Rp <?= number_format($avgPrice ?? 0, 0, ',', '.') ?></div>
              <div class="adm-stat-card-lbl">Rata-rata Harga</div>
            </div>
          </div>

          <!-- Platform bar chart -->
          <div class="adm-form-card" style="margin-top:0">
            <div class="ls-panel-title" style="margin-bottom:16px">Distribusi Platform</div>
            <?php
              $platforms = ['tokopedia'=>['#42b549','Tokopedia'], 'shopee'=>['#ee4d2d','Shopee'], 'lazada'=>['#0f146b','Lazada'], 'blibli'=>['#0095d9','Blibli']];
              $total = max(1, $totalProducts ?? 1);
              foreach($platforms as $key => [$color, $label]):
                $count = $countByPlatform[$key] ?? 0;
                $pct = round($count / $total * 100);
            ?>
            <div class="adm-bar-row">
              <div class="adm-bar-label">
                <div class="ls-pf-dot" style="background:<?= $color ?>"></div>
                <?= $label ?>
              </div>
              <div class="adm-bar-track">
                <div class="adm-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
              </div>
              <div class="adm-bar-pct"><?= $pct ?>%</div>
              <div class="adm-bar-count"><?= $count ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div><!-- end secStats -->

      </div><!-- end ls-main -->
    </div><!-- end listing-shell -->
  </div><!-- end adminPage -->

  <!-- Toast (identik) -->
  <div class="toast" id="toast"></div>

  <!-- Delete Confirm Modal (memakai gaya modal yang sama) -->
  <div class="modal-overlay" id="deleteModal" onclick="if(event.target===this) closeDeleteModal()">
    <div class="modal-box">
      <div class="modal-icon">🗑️</div>
      <div class="modal-title">Hapus Produk?</div>
      <div class="modal-sub">Produk ini akan dihapus permanen dari database dan tidak bisa dikembalikan.</div>
      <div class="modal-actions">
        <button class="modal-btn-login" style="background:#e05252" id="confirmDeleteBtn" onclick="confirmDelete()">Hapus</button>
        <button class="modal-btn-register" onclick="closeDeleteModal()">Batal</button>
      </div>
      <button class="modal-close" onclick="closeDeleteModal()">Lanjutkan tanpa menghapus</button>
    </div>
  </div>

  <script src="./public/js/admin.js"></script>
  <script>
  /* ─────────────────────────────────────────
     SECTION SWITCHING
  ───────────────────────────────────────── */
  function switchSection(el) {
    document.querySelectorAll('.adm-menu-item').forEach(i => i.classList.remove('on'));
    el.classList.add('on');
    const target = el.dataset.section;
    document.querySelectorAll('.adm-section').forEach(s => s.classList.add('hidden'));
    document.getElementById('sec' + target.charAt(0).toUpperCase() + target.slice(1)).classList.remove('hidden');
  }

  /* ─────────────────────────────────────────
     PLATFORM TOGGLE (SIDEBAR FILTER)
  ───────────────────────────────────────── */
  function admTogglePf(el) {
    el.classList.toggle('on');
    const pf = el.dataset.pf;
    document.querySelectorAll('.adm-product-row').forEach(row => {
      const active = [...document.querySelectorAll('.ls-platform-row.on')].map(r => r.dataset.pf);
      row.style.display = active.includes(row.dataset.platform) ? '' : 'none';
    });
  }

  /* ─────────────────────────────────────────
     EMOJI PICKER (tanpa preview card)
  ───────────────────────────────────────── */
  function pickEmoji(e) {
    document.getElementById('fEmoji').value = e;
    // Preview card sudah dihapus, tidak perlu update preview
  }

  /* ─────────────────────────────────────────
     FORM SUBMIT
  ───────────────────────────────────────── */
  function submitProduct() {
    const required = [
      { id:'fName',     label:'Nama Produk' },
      { id:'fPlatform', label:'Platform' },
      { id:'fCategory', label:'Kategori' },
      { id:'fPrice',    label:'Harga Jual' },
      { id:'fUrl',      label:'URL Produk' },
    ];
    for(const f of required) {
      const el = document.getElementById(f.id);
      if(!el.value.trim()) {
        el.classList.add('adm-input-error');
        el.focus();
        showToast('⚠️ ' + f.label + ' wajib diisi!', 'warn');
        setTimeout(() => el.classList.remove('adm-input-error'), 1800);
        return;
      }
    }

    const data = {
      name:       document.getElementById('fName').value.trim(),
      platform:   document.getElementById('fPlatform').value,
      category:   document.getElementById('fCategory').value,
      price:      document.getElementById('fPrice').value,
      orig_price: document.getElementById('fOrigPrice').value,
      discount:   document.getElementById('fDiscount').value,
      rating:     document.getElementById('fRating').value,
      sold:       document.getElementById('fSold').value,
      emoji:      document.getElementById('fEmoji').value || '📦',
      condition:  document.querySelector('input[name="condition"]:checked')?.value || 'baru',
      url:        document.getElementById('fUrl').value.trim(),
      img_url:    document.getElementById('fImg').value.trim(),
      // tags dan description dihapus karena field-nya sudah tidak ada
    };

    const btn = document.querySelector('.adm-btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<span class="adm-spinner"></span> Menyimpan…';

    fetch(window.location.href, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Admin-Action': 'add-product' },
      body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
      if(res.success) {
        showToast('✓ Produk berhasil disimpan!');
        resetForm();
        appendProductCard(res.product || data);
      } else {
        showToast('❌ Gagal: ' + (res.message || 'Terjadi kesalahan'), 'error');
      }
    })
    .catch(() => {
      showToast('✓ Produk berhasil disimpan! (simulasi)');
      resetForm();
    })
    .finally(() => {
      btn.disabled = false;
      btn.innerHTML = '<span id="submitTxt">✓ Simpan Produk</span>';
    });
  }

  function appendProductCard(p) {
    const wrap = document.getElementById('productListWrap');
    const empty = wrap.querySelector('.ls-empty');
    if(empty) empty.remove();

    const rows  = wrap.querySelectorAll('.adm-product-row');
    const num   = rows.length + 1;
    const pfColors = { tokopedia:'#42b549', shopee:'#ee4d2d', lazada:'#0f146b', blibli:'#0095d9' };
    const pfAbbr   = { tokopedia:'T', shopee:'S', lazada:'L', blibli:'B' };
    const color = pfColors[p.platform] || '#888';
    const abbr  = pfAbbr[p.platform]  || '?';

    const card = document.createElement('div');
    card.className = 'ls-card adm-product-row';
    card.dataset.name     = (p.name || '').toLowerCase();
    card.dataset.platform = p.platform || '';
    card.style.animationDelay = '.05s';

    card.innerHTML = `
      <div class="ls-card-rank">${num}</div>
      <div class="ls-card-img">${p.emoji||'📦'}</div>
      <div class="ls-card-body">
        <div class="ls-card-top">
          <div>
            <div class="ls-card-name">${escHtml(p.name)}</div>
            <div class="ls-card-sub">${cap(p.platform)} · ${cap(p.category||'Lainnya')}</div>
          </div>
          <div class="ls-card-badges">${p.discount>0?`<span class="ls-badge ls-badge-cheap">-${p.discount}%</span>`:''}</div>
        </div>
        <div class="ls-platform-tag">
          <div class="ls-pf-logo" style="background:${color}">${abbr}</div>
          <span>${cap(p.platform)}</span>
          ${p.rating?`<span style="opacity:.4">·</span><span>★ ${p.rating}</span>`:''}
          ${p.sold?`<span style="opacity:.4">·</span><span>${parseInt(p.sold).toLocaleString('id')} terjual</span>`:''}
        </div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">
        <div style="display:flex;align-items:baseline;gap:6px;">
          <span class="ls-card-price">Rp ${parseInt(p.price).toLocaleString('id')}</span>
          ${p.orig_price>0?`<span class="ls-card-orig">Rp ${parseInt(p.orig_price).toLocaleString('id')}</span>`:''}
        </div>
        <div class="ls-card-actions">
          <button class="adm-btn-edit ls-btn-save" title="Edit">✏️</button>
          <button class="adm-btn-del ls-btn-save" title="Hapus" onclick="deleteProduct(0, this)">🗑️</button>
          <a class="ls-btn-visit" href="${escHtml(p.url)}" target="_blank">Lihat ↗</a>
        </div>
      </div>`;
    wrap.prepend(card);

    const stat = document.getElementById('statTotal');
    if(stat) stat.textContent = parseInt(stat.textContent||0)+1;
  }

  function cap(s) { return s ? s.charAt(0).toUpperCase()+s.slice(1) : ''; }
  function escHtml(s) { const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }

  /* ─────────────────────────────────────────
     RESET FORM
  ───────────────────────────────────────── */
  function resetForm() {
    ['fName','fPlatform','fCategory','fPrice','fOrigPrice','fDiscount','fRating','fSold','fUrl','fImg'].forEach(id => {
      const el = document.getElementById(id);
      if(el) el.value = '';
    });
    document.getElementById('fEmoji').value = '';
    const r = document.querySelector('input[name="condition"][value="baru"]');
    if(r) r.checked = true;
  }

  /* ─────────────────────────────────────────
     PRODUCT LIST SEARCH
  ───────────────────────────────────────── */
  function filterProductList() {
    const q = document.getElementById('admSearch').value.toLowerCase();
    document.querySelectorAll('.adm-product-row').forEach(row => {
      const match = (row.dataset.name||'').includes(q) || (row.dataset.platform||'').includes(q);
      row.style.display = match ? '' : 'none';
    });
  }

  /* ─────────────────────────────────────────
     DELETE PRODUCT
  ───────────────────────────────────────── */
  let _deleteTarget = null;
  function deleteProduct(id, btn) {
    _deleteTarget = { id, btn };
    document.getElementById('deleteModal').classList.add('visible');
  }
  function closeDeleteModal() { document.getElementById('deleteModal').classList.remove('visible'); _deleteTarget = null; }
  function confirmDelete() {
    if(!_deleteTarget) return;
    const card = _deleteTarget.btn?.closest('.ls-card');
    if(card) { card.style.opacity='0'; card.style.transform='scale(.95)'; card.style.transition='all .3s'; setTimeout(()=>card.remove(),300); }
    closeDeleteModal();
    showToast('🗑️ Produk dihapus');
    const stat = document.getElementById('statTotal');
    if(stat) stat.textContent = Math.max(0, parseInt(stat.textContent||0)-1);

    if(_deleteTarget.id) {
      fetch(window.location.href, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-Admin-Action':'delete-product'},
        body: JSON.stringify({ id: _deleteTarget.id })
      }).catch(()=>{});
    }
  }

  /* ─────────────────────────────────────────
     EDIT (redirect/stub)
  ───────────────────────────────────────── */
  function editProduct(id) { showToast('✏️ Buka form edit produk #' + id); }

  /* ─────────────────────────────────────────
     USER DROPDOWN (identik list.php)
  ───────────────────────────────────────── */
  function toggleDropdown() { document.getElementById('userChipWrap')?.classList.toggle('open'); }
  document.addEventListener('click', e => {
    const wrap = document.getElementById('userChipWrap');
    if(wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
  });
  function doLogout() { window.location.href = '<?= (BASE_URL ?? '/') . 'logout' ?>'; }

  /* ─────────────────────────────────────────
     AVATAR INITIALS (identik list.php)
  ───────────────────────────────────────── */
  document.querySelectorAll('[data-avatar]').forEach(el => {
    const name = el.dataset.avatar || '';
    const parts = name.trim().split(' ');
    el.textContent = (parts[0]?.[0]||'') + (parts[1]?.[0]||'');
    el.textContent = el.textContent.toUpperCase() || '?';
  });

  /* ─────────────────────────────────────────
     TOAST (identik list.php)
  ───────────────────────────────────────── */
  let _toastTimer;
  function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = type === 'warn'  ? 'rgba(231,111,81,.92)'
                        : type === 'error' ? 'rgba(224,82,82,.92)'
                        : 'rgba(42,157,143,.92)';
    t.classList.add('visible');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('visible'), 2800);
  }

  </script>
</body>
</html>