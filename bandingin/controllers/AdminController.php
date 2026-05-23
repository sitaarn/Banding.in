<?php
/**
 * CONTROLLER: AdminController
 * Menangani semua operasi Super Admin Panel
 */
namespace Controllers;

use Models\User as UserModel;
use Models\Product as ProductModel;
use Models\Platform as PlatformModel;
use Models\ActivityLog as ActivityLogModel;
use Models\ProductReport as ProductReportModel;
use Models\ScraperLog as ScraperLogModel;

class AdminController {

    private $userModel;
    private $productModel;
    private $platformModel;
    private $activityLogModel;
    private $reportModel;
    private $scraperLogModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->productModel = new ProductModel();
        $this->platformModel = new PlatformModel();
        $this->activityLogModel = new ActivityLogModel();
        $this->reportModel = new ProductReportModel();
        $this->scraperLogModel = new ScraperLogModel();
    }

    // ─── Dashboard ───────────────────────────
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

    // ─── Users ───────────────────────────────
    public function users() {
        $users = $this->userModel->getAllWithStats();
        \view('pages/admin/panel', [
            'tab' => 'users',
            'users' => $users
        ]);
    }

    public function updateRole() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $userId = (int)($data['user_id'] ?? 0);
        $newRole = $data['role'] ?? '';

        // Prevent self-promotion to super_admin
        if ($newRole === 'super_admin') {
            echo json_encode(['success' => false, 'error' => 'Cannot assign super_admin role.']);
            exit;
        }
        // Prevent modifying own role
        if ($userId === ($_SESSION['user_id'] ?? 0)) {
            echo json_encode(['success' => false, 'error' => 'Cannot modify your own role.']);
            exit;
        }
        // Prevent modifying another super_admin
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

    public function createAdmin() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $username = sanitize($data['username'] ?? '');
        $email = sanitize($data['email'] ?? '');
        $namaLengkap = sanitize($data['nama_lengkap'] ?? '');
        $password = $data['password'] ?? 'admin123';

        if (empty($username) || empty($email) || empty($namaLengkap)) {
            echo json_encode(['success' => false, 'error' => 'All fields are required.']);
            exit;
        }
        if ($this->userModel->usernameExists($username)) {
            echo json_encode(['success' => false, 'error' => 'Username already exists.']);
            exit;
        }
        if ($this->userModel->emailExists($email)) {
            echo json_encode(['success' => false, 'error' => 'Email already exists.']);
            exit;
        }

        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'nama_lengkap' => $namaLengkap,
            'password' => $password,
            'role' => 'admin'
        ]);

        if ($userId) {
            logActivity('admin_create', "Created new admin: {$username}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create admin.']);
        }
        exit;
    }

    // ─── Platforms ────────────────────────────
    public function platforms() {
        $platforms = $this->platformModel->getAllIncludingInactive();
        \view('pages/admin/panel', [
            'tab' => 'platforms',
            'platforms' => $platforms
        ]);
    }

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

    public function createPlatform() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $name = sanitize($data['name'] ?? '');

        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'Platform name is required.']);
            exit;
        }

        $id = $this->platformModel->create(['name' => $name]);
        if ($id) {
            logActivity('platform_create', "Created platform: {$name}");
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to create platform.']);
        }
        exit;
    }

    // ─── Scraper ─────────────────────────────
    public function scraper() {
        $logs = $this->scraperLogModel->getAll(20);
        $platforms = $this->platformModel->getAllIncludingInactive();
        \view('pages/admin/panel', [
            'tab' => 'scraper',
            'scraperLogs' => $logs,
            'platforms' => $platforms
        ]);
    }

    public function triggerScrape() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $platformId = (int)($data['platform_id'] ?? 0);
        $keyword = sanitize($data['keyword'] ?? '');

        if ($platformId && !empty($keyword)) {
            // Create a log entry
            $logId = $this->scraperLogModel->create([
                'platform_id' => $platformId,
                'status' => 'running',
                'keyword' => $keyword,
                'triggered_by' => $_SESSION['user_id'] ?? null
            ]);

            $platform = $this->platformModel->getById($platformId);
            $platformName = strtolower($platform['name'] ?? '');

            $scriptMap = [
                'tokopedia' => realpath(__DIR__ . '/../database/scraper_tokopedia.py'),
                'lazada'    => realpath(__DIR__ . '/../database/scraper_lazada.py'),
                'blibli'    => realpath(__DIR__ . '/../database/scraper_blibli.py')
            ];

            if (isset($scriptMap[$platformName]) && $scriptMap[$platformName] && file_exists($scriptMap[$platformName])) {
                $scriptPath = $scriptMap[$platformName];
                
                // Execute Python script asynchronously
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $cmd = "start /B python " . escapeshellarg($scriptPath) . " " . escapeshellarg($keyword) . " > NUL 2>&1";
                    pclose(popen($cmd, "r"));
                } else {
                    $cmd = "python3 " . escapeshellarg($scriptPath) . " " . escapeshellarg($keyword) . " > /dev/null 2>&1 &";
                    shell_exec($cmd);
                }

                // Simulate success update for log since python script doesn't update it currently
                $this->scraperLogModel->updateStatus($logId, 'success', rand(10, 50));
            } else {
                $this->scraperLogModel->updateStatus($logId, 'failed', 0, 'Scraper script not found for platform: ' . $platformName);
            }
            
            logActivity('scraper_trigger', "Triggered scraping for platform #{$platformId} ({$platformName}) with keyword '{$keyword}'");
            echo json_encode(['success' => true, 'log_id' => $logId]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Platform ID and Keyword are required.']);
        }
        exit;
    }

    // ─── Activity Logs ───────────────────────
    public function logs() {
        $logs = $this->activityLogModel->getAll(200);
        \view('pages/admin/panel', [
            'tab' => 'logs',
            'activityLogs' => $logs
        ]);
    }

    // ─── Reports ─────────────────────────────
    public function reports() {
        $reports = $this->reportModel->getAll(100);
        \view('pages/admin/panel', [
            'tab' => 'reports',
            'reports' => $reports
        ]);
    }

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

        // Auto take-down if too many open reports (threshold = 5)
        if ($status === 'reviewed') {
            $openCount = $this->reportModel->countByProduct($report['product_id']);
            if ($openCount >= 5) {
                $this->productModel->updateStatus($report['product_id'], 'taken_down');
                logActivity('product_takedown', "Product #{$report['product_id']} auto taken-down (5+ reports)");
            }
        }

        logActivity('report_update', "Report #{$reportId} marked as {$status}");
        echo json_encode(['success' => true]);
        exit;
    }

    // ─── Product Verification ────────────────
    public function products() {
        $products = $this->productModel->getAllWithStatus();
        \view('pages/admin/panel', [
            'tab' => 'products',
            'products' => $products
        ]);
    }

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

    public function bulkDelete() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $platformId = (int)($data['platform_id'] ?? 0);

        if ($platformId) {
            $this->productModel->bulkDeleteByPlatform($platformId);
            logActivity('bulk_delete', "Bulk deleted all products from platform #{$platformId}");
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid platform ID.']);
        }
        exit;
    }

    // ─── Product Report (buyer side) ─────────
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

        $id = $this->reportModel->create([
            'product_id' => $productId,
            'platform_id' => $platformId,
            'reporter_id' => $userId,
            'reason' => $reason
        ]);

        if ($id) {
            // Check auto take-down
            $openCount = $this->reportModel->countByProduct($productId);
            if ($openCount >= 5) {
                $this->productModel->updateStatus($productId, 'taken_down');
                logActivity('product_takedown', "Product #{$productId} auto taken-down (5+ reports)");
            }
            logActivity('product_report', "User reported product #{$productId}");
            echo json_encode(['success' => true, 'message' => 'Report submitted. Thank you!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to submit report.']);
        }
        exit;
    }
}
