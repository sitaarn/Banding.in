<?php
/**
 * ============================================
 * CONTROLLER: AdminController
 * ============================================
 * 
 * Controller untuk Super Admin Panel.
 * Mengelola seluruh sistem:
 * - Dashboard (statistik ringkasan)
 * - User management (CRUD, role, aktif/nonaktif)
 * - Platform management (e-commerce)
 * - Web scraper (trigger & monitor)
 * - Activity logs (audit trail)
 * - Product reports (laporan user)
 * - Product verification (approve/reject dari seller)
 */
namespace Controllers;

use Models\User as UserModel;
use Models\Product as ProductModel;
use Models\Platform as PlatformModel;
use Models\ActivityLog as ActivityLogModel;
use Models\ProductReport as ProductReportModel;
use Models\ScraperLog as ScraperLogModel;

class AdminController {

    private $userModel;         // Model user
    private $productModel;      // Model produk
    private $platformModel;     // Model platform e-commerce
    private $activityLogModel;  // Model log aktivitas
    private $reportModel;       // Model laporan produk
    private $scraperLogModel;   // Model log scraper

    /** Constructor: inisialisasi semua model yang dibutuhkan */
    public function __construct() {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->platformModel = new PlatformModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->reportModel = new ProductReportModel();
        $this->scraperLogModel = new ScraperLogModel();
    }

    // ═══════════════════════════════════════════
    //  DASHBOARD - Halaman utama admin
    // ═══════════════════════════════════════════

    /** Tampilkan dashboard dengan statistik ringkasan sistem */
    public function dashboard() {
        $stats = [
            'total_users' => $this->userModel->count(),
            'total_sellers' => $this->userModel->countByRole('seller'),
            'total_admins' => $this->userModel->countByRole('admin'),
            'total_products' => $this->productModel->count(),
            'pending_products' => $this->productModel->countByStatus('pending'),
            'open_reports' => $this->reportModel->countOpen(),
        ];
        $recentLogs = $this->activityLogModel->getRecent(5);
        
        \view('pages/admin/panel', [
            'tab' => 'dashboard',
            'stats' => $stats,
            'recentLogs' => $recentLogs
        ]);
    }

    // ═══════════════════════════════════════════
    //  USER MANAGEMENT
    // ═══════════════════════════════════════════

    /** Tampilkan daftar semua user dengan statistik (jumlah produk & favorit) */
    public function users() {
        $users = $this->userModel->getAllWithStats();
        \view('pages/admin/panel', [
            'tab' => 'users',
            'users' => $users
        ]);
    }

    /**
     * Ubah role user (via AJAX).
     * Proteksi: tidak bisa assign super_admin, ubah role sendiri, atau ubah super_admin lain.
     */
    public function updateRole() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = (int)($data['user_id'] ?? 0);
        $newRole = $data['role'] ?? '';

        // Proteksi keamanan
        if ($newRole === 'super_admin') {
            echo json_encode(['success' => false, 'error' => 'Cannot assign super_admin role.']);
            exit;
        }
        if ($userId === ($_SESSION['user_id'] ?? 0)) {
            echo json_encode(['success' => false, 'error' => 'Cannot modify your own role.']);
            exit;
        }
        $target = $this->userModel->getById($userId);
        if ($target && $target['role'] === 'super_admin') {
            echo json_encode(['success' => false, 'error' => 'Cannot modify another super admin.']);
            exit;
        }

        $valid = in_array($newRole, ['user', 'seller', 'admin']);
        if ($valid && $userId) {
            $this->userModel->updateRole($userId, $newRole);
            logActivity('role_change', "Changed user #{$userId} role to {$newRole}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid data.']);
        }
        exit;
    }

    /** Toggle aktif/nonaktif user (via AJAX). Tidak bisa nonaktifkan diri sendiri atau super_admin. */
    public function toggleActive() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = (int)($data['user_id'] ?? 0);

        if ($userId === ($_SESSION['user_id'] ?? 0)) {
            echo json_encode(['success' => false, 'error' => 'Cannot deactivate yourself.']);
            exit;
        }
        $target = $this->userModel->getById($userId);
        if ($target && $target['role'] === 'super_admin') {
            echo json_encode(['success' => false, 'error' => 'Cannot deactivate a super admin.']);
            exit;
        }

        if ($userId) {
            $this->userModel->toggleActive($userId);
            logActivity('user_toggle_active', "Toggled active status for user #{$userId}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
        }
        exit;
    }

    /** Hapus user (via AJAX). Tidak bisa hapus diri sendiri atau super_admin. */
    public function deleteUser() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = (int)($data['user_id'] ?? 0);

        if ($userId === ($_SESSION['user_id'] ?? 0)) {
            echo json_encode(['success' => false, 'error' => 'Cannot delete yourself.']);
            exit;
        }
        $target = $this->userModel->getById($userId);
        if ($target && $target['role'] === 'super_admin') {
            echo json_encode(['success' => false, 'error' => 'Cannot delete a super admin.']);
            exit;
        }

        if ($userId) {
            $this->userModel->delete($userId);
            logActivity('user_delete', "Deleted user #{$userId} ({$target['username']})");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
        }
        exit;
    }

    /** Reset password user ke default 'password123' (via AJAX) */
    public function resetPassword() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = (int)($data['user_id'] ?? 0);

        if ($userId) {
            $this->userModel->resetPassword($userId);
            logActivity('password_reset', "Reset password for user #{$userId}");
            echo json_encode(['success' => true, 'message' => 'Password reset to: password123']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid user ID.']);
        }
        exit;
    }

    // ═══════════════════════════════════════════
    //  PLATFORM MANAGEMENT
    // ═══════════════════════════════════════════

    /** Tampilkan daftar semua platform e-commerce (termasuk yang nonaktif) */
    public function platforms() {
        $platforms = $this->platformModel->getAllIncludingInactive();
        \view('pages/admin/panel', [
            'tab' => 'platforms',
            'platforms' => $platforms
        ]);
    }

    /** Toggle aktif/nonaktif platform e-commerce (via AJAX) */
    public function togglePlatform() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $platformId = (int)($data['platform_id'] ?? 0);

        if ($platformId) {
            $this->platformModel->toggleActive($platformId);
            logActivity('platform_toggle', "Toggled platform #{$platformId}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid platform ID.']);
        }
        exit;
    }

    // ═══════════════════════════════════════════
    //  SCRAPER MANAGEMENT
    // ═══════════════════════════════════════════

    /** Tampilkan halaman scraper dengan log terbaru dan daftar platform */
    public function scraper() {
        $logs = $this->scraperLogModel->getAll(20);
        $platforms = $this->platformModel->getAllIncludingInactive();
        \view('pages/admin/panel', [
            'tab' => 'scraper',
            'scraperLogs' => $logs,
            'platforms' => $platforms
        ]);
    }

    /**
     * Trigger scraper Python untuk scraping produk dari e-commerce (via AJAX).
     * 
     * Alur:
     * 1. Buat log entry di DB (status: running)
     * 2. Cari script Python sesuai platform
     * 3. Jalankan script secara async (background process)
     * 4. Script Python akan update log status sendiri saat selesai
     */
    public function triggerScrape() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $platformId = (int)($data['platform_id'] ?? 0);
        $keyword = sanitize($data['keyword'] ?? '');

        if ($platformId && !empty($keyword)) {
            // Buat entry log scraper (status awal: running)
            $logId = $this->scraperLogModel->create([
                'platform_id' => $platformId,
                'status' => 'running',
                'keyword' => $keyword,
                'triggered_by' => $_SESSION['user_id'] ?? null
            ]);

            // Mapping platform ke script Python
            $platform = $this->platformModel->getById($platformId);
            $platformName = strtolower($platform['name'] ?? '');

            $scriptMap = [
                'tokopedia' => realpath(__DIR__ . '/../database/scraper_tokopedia.py'),
                'lazada'    => realpath(__DIR__ . '/../database/scraper_lazada.py'),
                'blibli'    => realpath(__DIR__ . '/../database/scraper_blibli.py')
            ];

            if (isset($scriptMap[$platformName]) && $scriptMap[$platformName] && file_exists($scriptMap[$platformName])) {
                $scriptPath = $scriptMap[$platformName];
                $scriptDir = dirname($scriptPath);
                
                // Jalankan script Python secara async (background)
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // Windows: gunakan cmd /c dengan start /B untuk background
                    $cmd = 'cmd /c "cd /d ' . escapeshellarg($scriptDir) . ' && python ' . escapeshellarg($scriptPath) . ' ' . escapeshellarg($keyword) . ' ' . escapeshellarg($logId) . ' > NUL 2>&1"';
                    pclose(popen("start /B " . $cmd, "r"));
                } else {
                    // Linux/Mac: gunakan & untuk background process
                    $cmd = "cd " . escapeshellarg($scriptDir) . " && python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($keyword) . " " . escapeshellarg($logId) . " > /dev/null 2>&1 &";
                    shell_exec($cmd);
                }

            } else {
                // Script tidak ditemukan → update log status jadi failed
                $this->scraperLogModel->updateStatus($logId, 'failed', 0, 'Scraper script not found for platform: ' . $platformName);
            }
            
            logActivity('scraper_trigger', "Triggered scraping for platform #{$platformId} ({$platformName}) with keyword '{$keyword}'");
            echo json_encode(['success' => true, 'log_id' => $logId]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Platform ID and Keyword are required.']);
        }
        exit;
    }

    // ═══════════════════════════════════════════
    //  ACTIVITY LOGS
    // ═══════════════════════════════════════════

    /** Tampilkan riwayat aktivitas sistem (200 log terbaru) */
    public function logs() {
        $logs = $this->activityLogModel->getAll(200);
        \view('pages/admin/panel', [
            'tab' => 'logs',
            'activityLogs' => $logs
        ]);
    }

    // ═══════════════════════════════════════════
    //  PRODUCT REPORTS (Laporan produk dari user)
    // ═══════════════════════════════════════════

    /** Tampilkan daftar laporan produk dari user (100 terbaru) */
    public function reports() {
        $reports = $this->reportModel->getAll(100);
        \view('pages/admin/panel', [
            'tab' => 'reports',
            'reports' => $reports
        ]);
    }

    /**
     * Update status laporan (via AJAX): reviewed atau dismissed.
     * Jika laporan di-reviewed dan produk punya >= 5 laporan terbuka → auto delete produk.
     */
    public function updateReport() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $reportId = (int)($data['report_id'] ?? 0);
        $status = $data['status'] ?? '';

        if (!in_array($status, ['reviewed', 'dismissed'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid status.']);
            exit;
        }

        $report = $this->reportModel->getById($reportId);
        if (!$report) {
            echo json_encode(['success' => false, 'error' => 'Report not found.']);
            exit;
        }

        $this->reportModel->updateStatus($reportId, $status);

        // Auto-delete produk jika sudah >= 5 laporan yang di-review
        if ($status === 'reviewed') {
            $openCount = $this->reportModel->countByProduct($report['product_id']);
            if ($openCount >= 5) {
                $this->productModel->delete($report['product_id']);
                logActivity('product_delete', "Product #{$report['product_id']} auto deleted (5+ reports)");
            }
        }

        logActivity('report_update', "Report #{$reportId} marked as {$status}");
        echo json_encode(['success' => true]);
        exit;
    }

    // ═══════════════════════════════════════════
    //  PRODUCT VERIFICATION (Verifikasi produk seller)
    // ═══════════════════════════════════════════

    /** Tampilkan semua produk dengan status (pending, approved, rejected, dll) */
    public function products() {
        $products = $this->productModel->getAllWithStatus();
        \view('pages/admin/panel', [
            'tab' => 'products',
            'products' => $products
        ]);
    }

    /** Ubah status verifikasi produk: approved, rejected, atau taken_down (via AJAX) */
    public function verifyProduct() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $productId = (int)($data['product_id'] ?? 0);
        $status = $data['status'] ?? '';

        if (!in_array($status, ['approved', 'rejected', 'taken_down'])) {
            echo json_encode(['success' => false, 'error' => 'Invalid status.']);
            exit;
        }

        if ($productId) {
            $this->productModel->updateStatus($productId, $status);
            logActivity('product_verify', "Product #{$productId} set to {$status}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid product ID.']);
        }
        exit;
    }

    /** Hapus produk dan update semua laporan terkait (via AJAX) */
    public function deleteProduct() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $productId = (int)($data['product_id'] ?? 0);

        if ($productId) {
            $product = $this->productModel->getById($productId);
            if ($product) {
                // Tandai laporan terbuka sebagai reviewed & simpan snapshot nama produk
                $this->reportModel->markAsReviewedAndSnapshot($productId, $product['name']);
            }
            $this->productModel->delete($productId);
            logActivity('product_delete', "Deleted product #{$productId}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid product ID.']);
        }
        exit;
    }

    /** Hapus beberapa produk sekaligus dan update semua laporan terkait (via AJAX) */
    public function deleteMultipleProducts() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $productIds = $data['product_ids'] ?? [];

        if (is_array($productIds) && !empty($productIds)) {
            $deletedCount = 0;
            foreach ($productIds as $id) {
                $id = (int)$id;
                if ($id > 0) {
                    $product = $this->productModel->getById($id);
                    if ($product) {
                        // Tandai laporan terbuka sebagai reviewed & simpan snapshot nama produk
                        $this->reportModel->markAsReviewedAndSnapshot($id, $product['name']);
                    }
                    $this->productModel->delete($id);
                    $deletedCount++;
                }
            }
            logActivity('product_delete_multiple', "Deleted {$deletedCount} products");
            echo json_encode(['success' => true, 'deleted_count' => $deletedCount]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No products selected.']);
        }
        exit;
    }

    // ═══════════════════════════════════════════
    //  PRODUCT REPORT (dari sisi buyer/user)
    // ═══════════════════════════════════════════

    /**
     * Kirim laporan produk dari user (via AJAX).
     * Satu user hanya bisa melaporkan satu produk sekali.
     * Jika total laporan terbuka >= 5 → produk otomatis dihapus.
     */
    public function submitReport() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $productId = (int)($data['product_id'] ?? 0);
        $platformId = isset($data['platform_id']) ? (int)$data['platform_id'] : null;
        $reason = sanitize($data['reason'] ?? '');
        $userId = $_SESSION['user_id'] ?? null;

        if (!$productId || !$userId) {
            echo json_encode(['success' => false, 'error' => 'Invalid data.']);
            exit;
        }

        // Cek apakah user sudah pernah melaporkan produk ini
        if ($this->reportModel->hasUserReported($productId, $userId)) {
            echo json_encode(['success' => false, 'error' => 'Anda sudah pernah melaporkan produk ini.']);
            exit;
        }

        try {
            $id = $this->reportModel->create([
                'product_id' => $productId,
                'platform_id' => $platformId,
                'reporter_id' => $userId,
                'reason' => $reason
            ]);

            if ($id) {
                // Auto-delete jika sudah >= 5 laporan terbuka
                $openCount = $this->reportModel->countByProduct($productId);
                if ($openCount >= 5) {
                    $this->productModel->delete($productId);
                    logActivity('product_delete', "Product #{$productId} auto deleted (5+ reports)");
                }
                logActivity('product_report', "User reported product #{$productId}");
                echo json_encode(['success' => true, 'message' => 'Report submitted. Thank you!']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to submit report.']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}
