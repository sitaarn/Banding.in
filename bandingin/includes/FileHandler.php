<?php
/**
 * 
 * CLASS: FileHandler
 * Menangani upload, validasi, dan penghapusan file
 * Praktikum Aplikasi Web - Universitas Tidar
 * 
 */

class FileHandler {
    private $uploadPath;
    private $allowedExtensions;
    private $allowedMimeTypes;
    private $maxSize;
    private $errors = [];

    /**
     * Constructor
     */
    public function __construct($config = []) {
        $this->uploadPath = $config['upload_path'] ?? UPLOADS_PATH;
        $this->allowedExtensions = $config['allowed_extensions'] ?? ALLOWED_EXTENSIONS;
        $this->allowedMimeTypes = $config['allowed_mime_types'] ?? ALLOWED_MIME_TYPES;
        $this->maxSize = $config['max_size'] ?? MAX_FILE_SIZE;
    }

    /**
     * Upload file
     * @param array $file - $_FILES['field_name']
     * @param string $subFolder - Sub folder tujuan
     * @param string|null $customName - Nama custom (opsional)
     * @return string|false - Nama file baru atau false jika gagal
     */
    public function upload($file, $subFolder = '', $customName = null) {
        $this->errors = [];

        // Validasi file
        if (!$this->validate($file)) {
            return false;
        }

        // Tentukan path upload
        $targetPath = rtrim($this->uploadPath, '/');
        if (!empty($subFolder)) {
            $targetPath .= '/' . trim($subFolder, '/');
        }

        // Buat folder jika belum ada
        if (!is_dir($targetPath)) {
            if (!mkdir($targetPath, 0755, true)) {
                $this->errors[] = "Gagal membuat folder upload.";
                return false;
            }
        }

        // Generate nama file
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($customName) {
            $newFileName = $customName . '.' . $extension;
        } else {
            $newFileName = $this->generateFileName($extension);
        }

        // Path lengkap file
        $targetFile = $targetPath . '/' . $newFileName;

        // Pindahkan file
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $newFileName;
        }

        $this->errors[] = "Gagal memindahkan file.";
        return false;
    }

    /**
     * Validasi file
     */
    public function validate($file) {
        // Cek apakah file ada
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->errors[] = "Tidak ada file yang diupload.";
            return false;
        }

        // Cek error upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }

        // Cek ukuran file
        if ($file['size'] > $this->maxSize) {
            $maxMB = $this->maxSize / (1024 * 1024);
            $this->errors[] = "Ukuran file melebihi batas maksimal ({$maxMB} MB).";
            return false;
        }

        // Cek ekstensi
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            $this->errors[] = "Ekstensi file tidak diizinkan. Ekstensi yang diizinkan: " . implode(', ', $this->allowedExtensions);
            return false;
        }

        // Cek MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            $this->errors[] = "Tipe file tidak diizinkan.";
            return false;
        }

        return true;
    }

    /**
     * Hapus file
     */
    public function delete($fileName, $subFolder = '') {
        $targetPath = rtrim($this->uploadPath, '/');
        if (!empty($subFolder)) {
            $targetPath .= '/' . trim($subFolder, '/');
        }

        $filePath = $targetPath . '/' . $fileName;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    /**
     * Generate nama file unik
     */
    public function generateFileName($extension) {
        $timestamp = date('YmdHis');
        $random = bin2hex(random_bytes(8));
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Dapatkan pesan error upload
     */
    private function getUploadErrorMessage($errorCode) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize di php.ini).',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE di form).',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menyimpan file ke disk.',
            UPLOAD_ERR_EXTENSION => 'Upload dibatalkan oleh ekstensi PHP.'
        ];

        return $messages[$errorCode] ?? 'Error upload tidak diketahui.';
    }

    /**
     * Dapatkan semua error
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Dapatkan error pertama
     */
    public function getFirstError() {
        return $this->errors[0] ?? null;
    }

    /**
     * Cek apakah ada error
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Cek apakah file ada
     */
    public function exists($fileName, $subFolder = '') {
        $targetPath = rtrim($this->uploadPath, '/');
        if (!empty($subFolder)) {
            $targetPath .= '/' . trim($subFolder, '/');
        }

        return file_exists($targetPath . '/' . $fileName);
    }

    /**
     * Dapatkan URL file
     */
    public function getUrl($fileName, $subFolder = '') {
        $path = 'uploads';
        if (!empty($subFolder)) {
            $path .= '/' . trim($subFolder, '/');
        }
        return BASE_URL . $path . '/' . $fileName;
    }

    /**
     * Dapatkan path file
     */
    public function getPath($fileName, $subFolder = '') {
        $targetPath = rtrim($this->uploadPath, '/');
        if (!empty($subFolder)) {
            $targetPath .= '/' . trim($subFolder, '/');
        }
        return $targetPath . '/' . $fileName;
    }
}
