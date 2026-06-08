<?php
/**
 * ============================================
 * HELPER FUNCTIONS - VALIDATION
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Class Validator untuk validasi input form secara chainable.
 * Mendukung: required, email, minLength, maxLength, numeric,
 * integer, min, max, matches, pattern, alphanumeric, url, in, custom.
 * 
 * Penggunaan:
 *   $v = validate($_POST);
 *   $v->required('nama')->email('email')->minLength('password', 8);
 *   if ($v->isValid()) { ... }
 */

class Validator {
    private $errors = []; // Menyimpan error per field
    private $data = [];   // Data input yang akan divalidasi

    /** Constructor: terima array data yang akan divalidasi */
    public function __construct($data = []) {
        $this->data = $data;
    }

    /** Validasi: field wajib diisi (tidak boleh kosong) */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? "Field {$field} wajib diisi.";
        }
        return $this; // Return $this agar bisa di-chain
    }

    /** Validasi: format email harus valid */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "Format email tidak valid.";
            }
        }
        return $this;
    }

    /** Validasi: panjang string minimal $length karakter */
    public function minLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? "Field {$field} minimal {$length} karakter.";
        }
        return $this;
    }

    /** Validasi: panjang string maksimal $length karakter */
    public function maxLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? "Field {$field} maksimal {$length} karakter.";
        }
        return $this;
    }

    /** Validasi: harus berupa angka (termasuk desimal) */
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = $message ?? "Field {$field} harus berupa angka.";
            }
        }
        return $this;
    }

    /** Validasi: harus berupa bilangan bulat (integer) */
    public function integer($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
                $this->errors[$field] = $message ?? "Field {$field} harus berupa bilangan bulat.";
            }
        }
        return $this;
    }

    /** Validasi: nilai numerik minimal $value */
    public function min($field, $value, $message = null) {
        if (isset($this->data[$field]) && is_numeric($this->data[$field])) {
            if ($this->data[$field] < $value) {
                $this->errors[$field] = $message ?? "Field {$field} minimal {$value}.";
            }
        }
        return $this;
    }

    /** Validasi: nilai numerik maksimal $value */
    public function max($field, $value, $message = null) {
        if (isset($this->data[$field]) && is_numeric($this->data[$field])) {
            if ($this->data[$field] > $value) {
                $this->errors[$field] = $message ?? "Field {$field} maksimal {$value}.";
            }
        }
        return $this;
    }

    /** Validasi: nilai field harus sama dengan field lain (misal: confirm_password == password) */
    public function matches($field, $otherField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$otherField])) {
            if ($this->data[$field] !== $this->data[$otherField]) {
                $this->errors[$field] = $message ?? "Field {$field} tidak cocok dengan {$otherField}.";
            }
        }
        return $this;
    }

    /** Validasi: nilai harus cocok dengan regex pattern */
    public function pattern($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!preg_match($pattern, $this->data[$field])) {
                $this->errors[$field] = $message ?? "Format field {$field} tidak valid.";
            }
        }
        return $this;
    }

    /** Validasi: hanya boleh huruf dan angka (A-Z, a-z, 0-9) */
    public function alphanumeric($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!ctype_alnum($this->data[$field])) {
                $this->errors[$field] = $message ?? "Field {$field} hanya boleh huruf dan angka.";
            }
        }
        return $this;
    }

    /** Validasi: harus berupa URL yang valid */
    public function url($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                $this->errors[$field] = $message ?? "Format URL tidak valid.";
            }
        }
        return $this;
    }

    /** Validasi: nilai harus ada di dalam array pilihan yang diberikan */
    public function in($field, $values, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!in_array($this->data[$field], $values)) {
                $this->errors[$field] = $message ?? "Nilai field {$field} tidak valid.";
            }
        }
        return $this;
    }

    /** Validasi custom: pakai callback function sendiri */
    public function custom($field, $callback, $message = null) {
        if (isset($this->data[$field])) {
            if (!$callback($this->data[$field])) {
                $this->errors[$field] = $message ?? "Validasi field {$field} gagal.";
            }
        }
        return $this;
    }

    // ── Hasil Validasi ──

    /** Return true jika tidak ada error (validasi lolos semua) */
    public function isValid() {
        return empty($this->errors);
    }

    /** Return true jika ada minimal 1 error */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /** Ambil semua error (array key=field, value=pesan error) */
    public function getErrors() {
        return $this->errors;
    }

    /** Ambil error untuk satu field tertentu */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }

    /** Ambil pesan error pertama saja */
    public function getFirstError() {
        return reset($this->errors) ?: null;
    }

    /** Reset semua error */
    public function reset() {
        $this->errors = [];
        return $this;
    }

    /** Set data baru (dan reset error) */
    public function setData($data) {
        $this->data = $data;
        $this->errors = [];
        return $this;
    }
}

/**
 * Helper function untuk buat validator secara cepat.
 * Penggunaan: validate($_POST)->required('nama')->email('email');
 */
function validate($data) {
    return new Validator($data);
}
