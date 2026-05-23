<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$base = (defined('BASE_URL')) ? BASE_URL : 'http://localhost/bandingin/';
$currentTab = $tab ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel — Banding.in</title>
  <link rel="icon" href="<?= $base ?>public/images/logo-b.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $base ?>public/css/stylelanding.css">
  <link rel="stylesheet" href="<?= $base ?>public/css/admin.css">
  <style>html,body{overflow:auto;}</style>
</head>
<body>
  <div class="bg"></div>
  <div class="blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>
  <div class="grid-overlay"></div>

  <div class="admin-layout">
    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
      <a class="admin-sidebar-brand" href="<?= $base ?>landing">banding<em>.in</em></a>
      
      <div class="admin-sidebar-label">Main</div>
      <a class="admin-nav-item <?= $currentTab==='dashboard'?'active':'' ?>" href="<?= $base ?>admin/dashboard">
        <span class="admin-nav-icon"><i class="fa-solid fa-chart-pie"></i></span> Dashboard
      </a>
      
      <div class="admin-sidebar-label">Management</div>
      <a class="admin-nav-item <?= $currentTab==='users'?'active':'' ?>" href="<?= $base ?>admin/users">
        <span class="admin-nav-icon"><i class="fa-solid fa-users"></i></span> Users
      </a>
      <a class="admin-nav-item <?= $currentTab==='platforms'?'active':'' ?>" href="<?= $base ?>admin/platforms">
        <span class="admin-nav-icon"><i class="fa-solid fa-store"></i></span> Platforms
      </a>
      <a class="admin-nav-item <?= $currentTab==='products'?'active':'' ?>" href="<?= $base ?>admin/products">
        <span class="admin-nav-icon"><i class="fa-solid fa-box-open"></i></span> Products
        <?php if(isset($stats) && ($stats['pending_products'] ?? 0) > 0): ?>
          <span class="admin-nav-badge"><?= $stats['pending_products'] ?></span>
        <?php endif; ?>
      </a>
      
      <div class="admin-sidebar-label">System</div>
      <a class="admin-nav-item <?= $currentTab==='scraper'?'active':'' ?>" href="<?= $base ?>admin/scraper">
        <span class="admin-nav-icon"><i class="fa-solid fa-robot"></i></span> Scraper
      </a>
      <a class="admin-nav-item <?= $currentTab==='logs'?'active':'' ?>" href="<?= $base ?>admin/logs">
        <span class="admin-nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></span> Activity Logs
      </a>
      <a class="admin-nav-item <?= $currentTab==='reports'?'active':'' ?>" href="<?= $base ?>admin/reports">
        <span class="admin-nav-icon"><i class="fa-solid fa-flag"></i></span> Reports
        <?php if(isset($stats) && ($stats['open_reports'] ?? 0) > 0): ?>
          <span class="admin-nav-badge"><?= $stats['open_reports'] ?></span>
        <?php endif; ?>
      </a>

      <a class="admin-back-btn" href="<?= $base ?>landing">
        <i class="fa-solid fa-arrow-left"></i> Back to Site
      </a>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">

    <?php if($currentTab === 'dashboard'): ?>
    <!-- ═══════ DASHBOARD ═══════ -->
    <div class="admin-header">
      <div>
        <h1 class="admin-title">Dashboard</h1>
        <p class="admin-subtitle">Welcome, <?= e($_SESSION['nama_lengkap'] ?? 'Super Admin') ?></p>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon">👥</div>
        <div class="stat-card-value"><?= $stats['total_users'] ?? 0 ?></div>
        <div class="stat-card-label">Total Users</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">🏪</div>
        <div class="stat-card-value"><?= $stats['total_sellers'] ?? 0 ?></div>
        <div class="stat-card-label">Sellers</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">🛡️</div>
        <div class="stat-card-value"><?= $stats['total_admins'] ?? 0 ?></div>
        <div class="stat-card-label">Admins</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">📦</div>
        <div class="stat-card-value"><?= $stats['total_products'] ?? 0 ?></div>
        <div class="stat-card-label">Products</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">⏳</div>
        <div class="stat-card-value"><?= $stats['pending_products'] ?? 0 ?></div>
        <div class="stat-card-label">Pending Verification</div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon">🚩</div>
        <div class="stat-card-value"><?= $stats['open_reports'] ?? 0 ?></div>
        <div class="stat-card-label">Open Reports</div>
      </div>
    </div>

    <div class="admin-panel">
      <div class="admin-panel-title"><i class="fa-solid fa-clock-rotate-left text-cyan"></i> Recent Activity</div>
      <table class="admin-table">
        <thead><tr><th>User</th><th>Action</th><th>Description</th><th>Time</th></tr></thead>
        <tbody>
        <?php if(!empty($recentLogs)): foreach($recentLogs as $log): ?>
          <tr>
            <td><?= e($log['username'] ?? 'System') ?></td>
            <td><span class="status-badge active"><?= e($log['action']) ?></span></td>
            <td><?= e($log['description'] ?? '-') ?></td>
            <td class="text-soft"><?= $log['created_at'] ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" class="text-soft" style="text-align:center;">No activity yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif($currentTab === 'users'): ?>
    <!-- ═══════ USERS ═══════ -->
    <div class="admin-header">
      <div>
        <h1 class="admin-title">User Management</h1>
        <p class="admin-subtitle">Manage all registered users, change roles, and create admins.</p>
      </div>
      <button class="admin-btn primary" onclick="showCreateAdminModal()"><i class="fa-solid fa-plus"></i> Create Admin</button>
    </div>

    <div class="admin-panel">
      <table class="admin-table">
        <thead><tr><th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Products</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if(!empty($users)): foreach($users as $u): ?>
          <tr id="user-row-<?= $u['id'] ?>">
            <td>#<?= $u['id'] ?></td>
            <td style="color:var(--text-light);font-weight:500;"><?= e($u['username']) ?></td>
            <td><?= e($u['nama_lengkap']) ?></td>
            <td class="text-soft"><?= e($u['email']) ?></td>
            <td>
              <?php if($u['role'] !== 'super_admin'): ?>
              <select class="admin-select" style="width:auto;padding:4px 8px;font-size:0.72rem;" onchange="updateRole(<?= $u['id'] ?>, this.value)">
                <option value="user" <?= $u['role']==='user'?'selected':'' ?>>User</option>
                <option value="seller" <?= $u['role']==='seller'?'selected':'' ?>>Seller</option>
                <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
              </select>
              <?php else: ?>
              <span class="role-badge super_admin">Super Admin</span>
              <?php endif; ?>
            </td>
            <td><span class="status-badge <?= ($u['is_active'] ?? 1) ? 'active' : 'inactive' ?>"><?= ($u['is_active'] ?? 1) ? 'Active' : 'Inactive' ?></span></td>
            <td><?= $u['product_count'] ?? 0 ?></td>
            <td>
              <?php if($u['role'] !== 'super_admin'): ?>
              <div class="admin-btn-group">
                <button class="admin-btn warning" onclick="toggleActive(<?= $u['id'] ?>)" title="Toggle Active"><i class="fa-solid fa-power-off"></i></button>
                <button class="admin-btn" onclick="resetPassword(<?= $u['id'] ?>)" title="Reset Password"><i class="fa-solid fa-key"></i></button>
                <button class="admin-btn danger" onclick="deleteUser(<?= $u['id'] ?>, '<?= e($u['username']) ?>')" title="Delete"><i class="fa-solid fa-trash"></i></button>
              </div>
              <?php else: ?>
              <span class="text-soft">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif($currentTab === 'platforms'): ?>
    <!-- ═══════ PLATFORMS ═══════ -->
    <div class="admin-header">
      <div>
        <h1 class="admin-title">Platform Management</h1>
        <p class="admin-subtitle">Add, enable, or disable marketplace platforms.</p>
      </div>
    </div>

    <div class="admin-panel">
      <div class="admin-panel-title"><i class="fa-solid fa-plus-circle text-cyan"></i> Add New Platform</div>
      <div class="admin-form-row">
        <div class="admin-form-group">
          <label class="admin-form-label">Platform Name</label>
          <input type="text" class="admin-input" id="newPlatformName" placeholder="e.g. Bukalapak">
        </div>
        <button class="admin-btn primary" onclick="createPlatform()" style="height:42px;"><i class="fa-solid fa-plus"></i> Add</button>
      </div>
    </div>

    <div class="admin-panel">
      <div class="admin-panel-title"><i class="fa-solid fa-store text-cyan"></i> All Platforms</div>
      <table class="admin-table">
        <thead><tr><th>ID</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if(!empty($platforms)): foreach($platforms as $p): ?>
          <tr>
            <td>#<?= $p['id'] ?></td>
            <td style="color:var(--text-light);font-weight:500;"><?= e($p['name']) ?></td>
            <td><span class="status-badge <?= ($p['is_active'] ?? 1) ? 'active' : 'inactive' ?>"><?= ($p['is_active'] ?? 1) ? 'Active' : 'Inactive' ?></span></td>
            <td>
              <button class="admin-btn <?= ($p['is_active'] ?? 1) ? 'warning' : 'success' ?>" onclick="togglePlatform(<?= $p['id'] ?>)">
                <i class="fa-solid fa-<?= ($p['is_active'] ?? 1) ? 'pause' : 'play' ?>"></i>
                <?= ($p['is_active'] ?? 1) ? 'Disable' : 'Enable' ?>
              </button>
            </td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif($currentTab === 'products'): ?>
    <!-- ═══════ PRODUCTS ═══════ -->
    <div class="admin-header">
      <div>
        <h1 class="admin-title">Product Verification</h1>
        <p class="admin-subtitle">Review and approve seller-submitted products.</p>
      </div>
    </div>

    <!-- Bulk Delete -->
    <div class="admin-panel">
      <div class="admin-panel-title"><i class="fa-solid fa-trash text-red"></i> Bulk Delete</div>
      <div class="admin-form-row">
        <div class="admin-form-group">
          <label class="admin-form-label">Delete all products from platform:</label>
          <select class="admin-select" id="bulkDeletePlatform">
            <option value="">Choose platform...</option>
            <option value="1">Tokopedia</option>
            <option value="2">Lazada</option>
            <option value="3">Blibli</option>
          </select>
        </div>
        <button class="admin-btn danger" onclick="bulkDelete()" style="height:42px;"><i class="fa-solid fa-trash"></i> Delete All</button>
      </div>
    </div>

    <div class="admin-panel">
      <div class="admin-panel-title"><i class="fa-solid fa-box-open text-cyan"></i> All Products</div>
      <table class="admin-table">
        <thead><tr><th>ID</th><th>Product</th><th>Seller</th><th>Platform</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if(!empty($products)): foreach($products as $p): ?>
          <tr id="product-row-<?= $p['id'] ?>">
            <td>#<?= $p['id'] ?></td>
            <td style="color:var(--text-light);font-weight:500;"><?= e($p['name']) ?></td>
            <td><?= e($p['seller_name'] ?? '-') ?></td>
            <td><?= e($p['platform_name'] ?? '-') ?></td>
            <td>Rp <?= number_format($p['price'] ?? 0, 0, ',', '.') ?></td>
            <td><span class="status-badge <?= $p['status'] ?? 'approved' ?>"><?= ucfirst($p['status'] ?? 'approved') ?></span></td>
            <td>
              <div class="admin-btn-group">
                <?php if(($p['status'] ?? 'approved') === 'pending'): ?>
                  <button class="admin-btn success" onclick="verifyProduct(<?= $p['id'] ?>, 'approved')"><i class="fa-solid fa-check"></i> Approve</button>
                  <button class="admin-btn danger" onclick="verifyProduct(<?= $p['id'] ?>, 'rejected')"><i class="fa-solid fa-times"></i> Reject</button>
                <?php elseif(($p['status'] ?? 'approved') === 'approved'): ?>
                  <button class="admin-btn danger" onclick="verifyProduct(<?= $p['id'] ?>, 'taken_down')"><i class="fa-solid fa-ban"></i> Take Down</button>
                <?php elseif(($p['status'] ?? '') === 'rejected' || ($p['status'] ?? '') === 'taken_down'): ?>
                  <button class="admin-btn success" onclick="verifyProduct(<?= $p['id'] ?>, 'approved')"><i class="fa-solid fa-redo"></i> Restore</button>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="7" class="text-soft" style="text-align:center;">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif($currentTab === 'scraper'): ?>
    <!-- ═══════ SCRAPER ═══════ -->
    <div class="admin-header">
      <div>
        <h1 class="admin-title">Scraper Management</h1>
        <p class="admin-subtitle">Trigger scraping and view logs.</p>
      </div>
    </div>

    <div class="admin-panel">
      <div class="admin-panel-title"><i class="fa-solid fa-play text-cyan"></i> Trigger Scraping</div>
      <div class="admin-form-row">
        <div class="admin-form-group">
          <label class="admin-form-label">Select Platform</label>
          <select class="admin-select" id="scrapePlatform">
            <?php if(!empty($platforms)): foreach($platforms as $p): ?>
              <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
            <?php endforeach; endif; ?>
          </select>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Keyword (Optional)</label>
          <input type="text" class="admin-input" id="scrapeKeyword" placeholder="e.g. iPhone 15 Pro">
        </div>
        <button class="admin-btn primary" onclick="triggerScrape()" style="height:42px;"><i class="fa-solid fa-rocket"></i> Scrape Now</button>
      </div>
    </div>

    <div class="admin-panel">
      <div class="admin-panel-title"><i class="fa-solid fa-list text-cyan"></i> Scraper Logs</div>
      <table class="admin-table">
        <thead><tr><th>ID</th><th>Platform</th><th>Keyword</th><th>Status</th><th>Items</th><th>Triggered By</th><th>Started</th><th>Finished</th></tr></thead>
        <tbody>
        <?php if(!empty($scraperLogs)): foreach($scraperLogs as $sl): ?>
          <tr>
            <td>#<?= $sl['id'] ?></td>
            <td><?= e($sl['platform_name'] ?? '-') ?></td>
            <td style="color:var(--text-light);"><?= e($sl['keyword'] ?? '-') ?></td>
            <td><span class="status-badge <?= $sl['status'] ?>"><?= ucfirst($sl['status']) ?></span></td>
            <td><?= $sl['items_scraped'] ?? 0 ?></td>
            <td><?= e($sl['triggered_by_name'] ?? '-') ?></td>
            <td class="text-soft"><?= $sl['started_at'] ?? '-' ?></td>
            <td class="text-soft"><?= $sl['finished_at'] ?? '-' ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="8" class="text-soft" style="text-align:center;">No scraper logs yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif($currentTab === 'logs'): ?>
    <!-- ═══════ ACTIVITY LOGS ═══════ -->
    <div class="admin-header">
      <div>
        <h1 class="admin-title">Activity Logs</h1>
        <p class="admin-subtitle">View all system activity and user actions.</p>
      </div>
    </div>

    <div class="admin-panel">
      <table class="admin-table">
        <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
        <tbody>
        <?php if(!empty($activityLogs)): foreach($activityLogs as $al): ?>
          <tr>
            <td class="text-soft"><?= $al['created_at'] ?></td>
            <td style="color:var(--text-light);"><?= e($al['username'] ?? 'System') ?></td>
            <td><span class="status-badge active"><?= e($al['action']) ?></span></td>
            <td><?= e($al['description'] ?? '-') ?></td>
            <td class="text-soft"><?= e($al['ip_address'] ?? '-') ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="5" class="text-soft" style="text-align:center;">No activity logs yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php elseif($currentTab === 'reports'): ?>
    <!-- ═══════ REPORTS ═══════ -->
    <div class="admin-header">
      <div>
        <h1 class="admin-title">Product Reports</h1>
        <p class="admin-subtitle">Review price mismatch reports from buyers.</p>
      </div>
    </div>

    <div class="admin-panel">
      <table class="admin-table">
        <thead><tr><th>ID</th><th>Product</th><th>Platform</th><th>Reporter</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if(!empty($reports)): foreach($reports as $r): ?>
          <tr id="report-row-<?= $r['id'] ?>">
            <td>#<?= $r['id'] ?></td>
            <td style="color:var(--text-light);"><?= e($r['product_name'] ?? '-') ?></td>
            <td><?= e($r['platform_name'] ?? '-') ?></td>
            <td><?= e($r['reporter_username'] ?? '-') ?></td>
            <td><?= e($r['reason'] ?? '-') ?></td>
            <td><span class="status-badge <?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
            <td class="text-soft"><?= $r['created_at'] ?></td>
            <td>
              <?php if($r['status'] === 'open'): ?>
              <div class="admin-btn-group">
                <button class="admin-btn success" onclick="updateReport(<?= $r['id'] ?>, 'reviewed')"><i class="fa-solid fa-check"></i></button>
                <button class="admin-btn" onclick="updateReport(<?= $r['id'] ?>, 'dismissed')"><i class="fa-solid fa-times"></i></button>
              </div>
              <?php else: ?>
              <span class="text-soft">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="8" class="text-soft" style="text-align:center;">No reports yet.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php endif; ?>

    </main>
  </div>

  <!-- Create Admin Modal -->
  <div class="admin-modal-overlay" id="createAdminModal">
    <div class="admin-modal">
      <div class="admin-modal-title"><i class="fa-solid fa-user-plus text-cyan"></i> Create New Admin</div>
      <div class="admin-form-group" style="margin-bottom:12px;">
        <label class="admin-form-label">Username</label>
        <input type="text" class="admin-input" id="newAdminUsername" placeholder="admin_username">
      </div>
      <div class="admin-form-group" style="margin-bottom:12px;">
        <label class="admin-form-label">Full Name</label>
        <input type="text" class="admin-input" id="newAdminName" placeholder="Full Name">
      </div>
      <div class="admin-form-group" style="margin-bottom:12px;">
        <label class="admin-form-label">Email</label>
        <input type="email" class="admin-input" id="newAdminEmail" placeholder="email@example.com">
      </div>
      <div class="admin-form-group" style="margin-bottom:12px;">
        <label class="admin-form-label">Password</label>
        <input type="password" class="admin-input" id="newAdminPassword" placeholder="Default: admin123" value="admin123">
      </div>
      <div class="admin-modal-actions">
        <button class="admin-btn primary" onclick="createAdmin()" style="flex:1;justify-content:center;"><i class="fa-solid fa-check"></i> Create Admin</button>
        <button class="admin-btn" onclick="hideCreateAdminModal()" style="flex:1;justify-content:center;">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Confirm Modal -->
  <div class="admin-modal-overlay" id="confirmModal">
    <div class="admin-modal">
      <div class="admin-modal-title" id="confirmTitle">Confirm</div>
      <p style="color:var(--text-mid);font-size:0.85rem;" id="confirmText">Are you sure?</p>
      <div class="admin-modal-actions">
        <button class="admin-btn danger" id="confirmYes" style="flex:1;justify-content:center;">Yes, proceed</button>
        <button class="admin-btn" onclick="hideConfirm()" style="flex:1;justify-content:center;">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="admin-toast" id="adminToast"></div>

  <script>
  const BASE = '<?= $base ?>';

  // ── Toast ──
  function showToast(msg, isError = false) {
    const t = document.getElementById('adminToast');
    t.textContent = msg;
    t.className = 'admin-toast visible' + (isError ? ' error' : '');
    setTimeout(() => t.classList.remove('visible'), 3000);
  }

  // ── Confirm Modal ──
  let confirmCallback = null;
  function showConfirm(title, text, cb) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmText').textContent = text;
    confirmCallback = cb;
    document.getElementById('confirmModal').classList.add('visible');
    document.getElementById('confirmYes').onclick = () => { hideConfirm(); if(confirmCallback) confirmCallback(); };
  }
  function hideConfirm() { document.getElementById('confirmModal').classList.remove('visible'); }

  // ── Create Admin Modal ──
  function showCreateAdminModal() { document.getElementById('createAdminModal').classList.add('visible'); }
  function hideCreateAdminModal() { document.getElementById('createAdminModal').classList.remove('visible'); }

  // ── API Helper ──
  async function apiPost(url, data) {
    const res = await fetch(BASE + url, {
      method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
    });
    return res.json();
  }

  // ── User Management ──
  async function updateRole(userId, role) {
    const r = await apiPost('admin/users/update-role', {user_id: userId, role});
    if(r.success) { showToast('Role updated!'); } else { showToast(r.error || 'Failed', true); }
  }
  function toggleActive(userId) {
    showConfirm('Toggle Status', 'Toggle this user\'s active status?', async () => {
      const r = await apiPost('admin/users/toggle-active', {user_id: userId});
      if(r.success) { showToast('Status updated!'); setTimeout(()=>location.reload(), 800); } 
      else { showToast(r.error || 'Failed', true); }
    });
  }
  function deleteUser(userId, username) {
    showConfirm('Delete User', `Delete user "${username}"? This cannot be undone.`, async () => {
      const r = await apiPost('admin/users/delete', {user_id: userId});
      if(r.success) { showToast('User deleted!'); document.getElementById('user-row-'+userId)?.remove(); } 
      else { showToast(r.error || 'Failed', true); }
    });
  }
  function resetPassword(userId) {
    showConfirm('Reset Password', 'Reset password to "password123"?', async () => {
      const r = await apiPost('admin/users/reset-password', {user_id: userId});
      if(r.success) { showToast(r.message || 'Password reset!'); } 
      else { showToast(r.error || 'Failed', true); }
    });
  }
  async function createAdmin() {
    const data = {
      username: document.getElementById('newAdminUsername').value,
      nama_lengkap: document.getElementById('newAdminName').value,
      email: document.getElementById('newAdminEmail').value,
      password: document.getElementById('newAdminPassword').value
    };
    const r = await apiPost('admin/users/create', data);
    if(r.success) { showToast('Admin created!'); hideCreateAdminModal(); setTimeout(()=>location.reload(), 800); } 
    else { showToast(r.error || 'Failed', true); }
  }

  // ── Platform Management ──
  function togglePlatform(id) {
    showConfirm('Toggle Platform', 'Toggle this platform\'s active status?', async () => {
      const r = await apiPost('admin/platforms/toggle', {platform_id: id});
      if(r.success) { showToast('Platform updated!'); setTimeout(()=>location.reload(), 800); } 
      else { showToast(r.error || 'Failed', true); }
    });
  }
  async function createPlatform() {
    const name = document.getElementById('newPlatformName').value;
    if(!name) { showToast('Enter platform name', true); return; }
    const r = await apiPost('admin/platforms/create', {name});
    if(r.success) { showToast('Platform added!'); setTimeout(()=>location.reload(), 800); } 
    else { showToast(r.error || 'Failed', true); }
  }

  // ── Product Verification ──
  async function verifyProduct(id, status) {
    const r = await apiPost('admin/products/verify', {product_id: id, status});
    if(r.success) { showToast('Product ' + status + '!'); setTimeout(()=>location.reload(), 800); } 
    else { showToast(r.error || 'Failed', true); }
  }
  function bulkDelete() {
    const pid = document.getElementById('bulkDeletePlatform').value;
    if(!pid) { showToast('Select a platform first', true); return; }
    showConfirm('Bulk Delete', 'Delete ALL products from this platform? This cannot be undone!', async () => {
      const r = await apiPost('admin/products/bulk-delete', {platform_id: parseInt(pid)});
      if(r.success) { showToast('Bulk delete done!'); setTimeout(()=>location.reload(), 800); } 
      else { showToast(r.error || 'Failed', true); }
    });
  }

  // ── Scraper ──
  async function triggerScrape() {
    const pid = document.getElementById('scrapePlatform').value;
    const kw = document.getElementById('scrapeKeyword').value;
    if(!pid) { showToast('Select a platform', true); return; }
    if(!kw) { showToast('Enter a keyword to scrape', true); return; }
    
    showToast('Starting scraper for keyword: ' + kw);
    const r = await apiPost('admin/scraper/trigger', {platform_id: parseInt(pid), keyword: kw});
    if(r.success) { 
      showToast('Scraping triggered successfully!'); 
      setTimeout(()=>location.reload(), 1200); 
    } else { 
      showToast(r.error || 'Failed', true); 
    }
  }

  // ── Reports ──
  async function updateReport(id, status) {
    const r = await apiPost('admin/reports/update', {report_id: id, status});
    if(r.success) { showToast('Report updated!'); setTimeout(()=>location.reload(), 800); } 
    else { showToast(r.error || 'Failed', true); }
  }
  </script>
</body>
</html>
