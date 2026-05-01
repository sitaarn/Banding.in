    // Fungsi untuk update navigasi berdasarkan status login

    // function updateNav() {
    //   const navLinks = document.getElementById('navLinks');
    //   const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
    //   const userName = localStorage.getItem('userName') || 'User';
    //   const userEmail = localStorage.getItem('userEmail') || 'user@example.com';

    //   function getInitials(name) {
    //     return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    //   }

    //   if (isLoggedIn) {
    //     // Tampilkan user chip (tanpa Cari Produk) dan About Us
    //     navLinks.innerHTML = `
    //       <div class="user-chip-wrap" id="userChipWrap">
    //         <div class="user-chip" onclick="toggleDropdown()">
    //           <div class="user-avatar" id="navAvatar">${getInitials(userName)}</div>
    //           <span class="user-name" id="navName">${userName}</span>
    //           <div class="user-online"></div>
    //           <span class="user-chevron">▾</span>
    //         </div>
    //         <div class="user-dropdown" id="userDropdown">
    //           <div class="dropdown-info">
    //             <div class="dropdown-info-name" id="dropName">${userName}</div>
    //             <div class="dropdown-info-label" id="dropEmail">${userEmail}</div>
    //           </div>
    //           <a class="dropdown-item" href="http://localhost/hello-world/profile">
    //             <span class="dropdown-icon">👤</span> Profil Saya
    //           </a>
    //           <div class="dropdown-item logout" onclick="doLogout()">
    //             <span class="dropdown-icon">↩</span> Keluar
    //           </div>
    //         </div>
    //       </div>
    //       <button class="nav-btn" onclick="window.location.href='http://localhost/hello-world/aboutus'">About Us</button>
    //     `;
    //   } else {
    //     // Tampilkan Login dan About Us
    //     navLinks.innerHTML = `
    //       <button class="nav-btn" onclick="goToLogin()">Login</button>
    //       <button class="nav-btn" onclick="window.location.href='http://localhost/hello-world/aboutus'">About Us</button>
    //     `;
    //   }
    // }

    // Fungsi untuk toggle dropdown
    function toggleDropdown() {
      document.getElementById('userChipWrap').classList.toggle('open');
    }

    // Tutup dropdown jika klik di luar
    document.addEventListener('click', function(e) {
      const wrap = document.getElementById('userChipWrap');
      if (wrap && !wrap.contains(e.target)) {
        wrap.classList.remove('open');
      }
    });

    // Fungsi logout
    function doLogout() {
      localStorage.removeItem('loggedIn');
      localStorage.removeItem('userName');
      localStorage.removeItem('userEmail');
      window.location.href = 'http://localhost/hello-world/logout';// Refresh untuk update navigasi
    }

    // Fungsi login redirect
    function goToLogin() {
      window.location.href = 'http://localhost/hello-world/login';
    }

    // Panggil updateNav saat halaman dimuat
    window.addEventListener('DOMContentLoaded', updateNav);

    function goToListingPage(query) {
      window.location.href = 'http://localhost/hello-world/list?q='+ query;
    }

    // Toggle platform
    function togglePlatform(btn) {
      const p = btn.dataset.platform;
      if (p === 'all') {
        document.querySelectorAll('.pf-btn').forEach(b => b.classList.add('active'));
      } else {
        btn.classList.toggle('active');
        const allActive = [...document.querySelectorAll('.pf-btn[data-platform!="all"]')].every(b => b.classList.contains('active'));
        document.querySelector('.pf-btn[data-platform="all"]').classList.toggle('active', allActive);
      }
    }

    // Data dummy produk
    const mockData = {
      platforms: {
        tokopedia: { color: '#42b549', label: 'Tokopedia' },
        shopee:    { color: '#ee4d2d', label: 'Shopee' },
        lazada:    { color: '#0f146b', label: 'Lazada' },
        blibli:    { color: '#0095d9', label: 'Blibli' }
      },
      products: [
        { name: 'iPhone 15',       prices: { tokopedia: 12999000, shopee: 12749000, lazada: 12899000, blibli: 13100000 } },
        { name: 'Samsung Galaxy S24',  prices: { tokopedia: 8499000,  shopee: 8299000,  lazada: 8350000,  blibli: 8600000  } },
        { name: 'Nike Air Max',    prices: { tokopedia: 1850000,  shopee: 1799000,  lazada: 1920000,  blibli: 1875000  } },
        { name: 'Laptop Asus',     prices: { tokopedia: 9999000,  shopee: 9750000,  lazada: 9850000,  blibli: 10200000 } },
        { name: 'AirPods Pro',     prices: { tokopedia: 3499000,  shopee: 3299000,  lazada: 3350000,  blibli: 3600000  } },
        { name: 'Xiaomi Redmi',    prices: { tokopedia: 2199000,  shopee: 2099000,  lazada: 2150000,  blibli: 2299000  } },
        { name: 'Tas Ransel',      prices: { tokopedia: 350000,   shopee: 299000,   lazada: 320000,   blibli: 375000   } },
        { name: 'PS5',             prices: { tokopedia: 8999000,  shopee: 8799000,  lazada: 8850000,  blibli: 9100000  } }
      ]
    };

    // Fungsi pencarian (menampilkan preview)
    function doSearch() {
      const query = document.getElementById('searchInput').value.trim();
      if (!query) return;

      const area = document.getElementById('resultsArea');
      const ms = document.getElementById('mostSearched');
      ms.style.display = 'none';
      area.style.display = 'flex';
      area.innerHTML = '<div class="loading-dots"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';

      setTimeout(() => {
        const ap = [...document.querySelectorAll('.pf-btn[data-platform!="all"].active')].map(b => b.dataset.platform);
        if (!ap.length) {
          area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Pilih minimal satu platform dulu ya 😊</div>';
          return;
        }

        const match = mockData.products.find(p => p.name.toLowerCase().includes(query.toLowerCase())) || mockData.products[0];
        const entries = ap.map(p => ({ platform: p, price: match.prices[p] })).sort((a, b) => a.price - b.price);
        const minPrice = entries[0].price;

        area.innerHTML = `
          <div class="results-grid">
            ${entries.map(e => `
              <div class="result-card">
                <div class="result-left">
                  <span class="result-platform" style="background:${mockData.platforms[e.platform].color}">${mockData.platforms[e.platform].label}</span>
                  <span class="result-name">${match.name}</span>
                </div>
                <div style="display:flex;align-items:center">
                  <span class="result-price ${e.price === minPrice ? 'cheapest' : ''}">Rp ${e.price.toLocaleString('id-ID')}</span>
                  ${e.price === minPrice ? '<span class="badge-cheapest">TERMURAH</span>' : ''}
                </div>
              </div>`).join('')}
            <div style="text-align:right;padding-top:8px;">
              <button
                onclick="goToListingPage('${match.name}')"
                style="padding:8px 20px;border-radius:999px;background:var(--text-dark);color:white;border:none;font-family:'DM Sans',sans-serif;font-size:.75rem;font-weight:500;cursor:pointer;letter-spacing:.04em;"
                onmouseover="this.style.background='var(--blue)'"
                onmouseout="this.style.background='var(--text-dark)'">
                Lihat Semua Hasil →
              </button>
            </div>
          </div>`;
      }, 900);
    }

    // Enter untuk mencari
    document.addEventListener('keydown', e => {
      if (e.key === 'Enter') doSearch();
    });