<?php
/**
 * =====================================================
 * HELPER FUNCTIONS - VALIDATION
 * Praktikum Aplikasi Web - Universitas Tidar
 * =====================================================
 */

class Validator {
    private $errors = [];
    private $data = [];

    /**
     * Constructor
     */
    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * Validasi field wajib diisi
     */
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = $message ?? "Field {$field} wajib diisi.";
        }
        return $this;
    }

    /**
     * Validasi email
     */
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "Format email tidak valid.";
            }
        }
        return $this;
    }

    /**
     * Validasi panjang minimum
     */
    public function minLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? "Field {$field} minimal {$length} karakter.";
        }
        return $this;
    }

    /**
     * Validasi panjang maksimum
     */
    public function maxLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? "Field {$field} maksimal {$length} karakter.";
        }
        return $this;
    }

    /**
     * Validasi angka
     */
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = $message ?? "Field {$field} harus berupa angka.";
            }
        }
        return $this;
    }

    /**
     * Validasi integer
     */
    public function integer($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
                $this->errors[$field] = $message ?? "Field {$field} harus berupa bilangan bulat.";
            }
        }
        return $this;
    }

    /**
     * Validasi nilai minimum
     */
    public function min($field, $value, $message = null) {
        if (isset($this->data[$field]) && is_numeric($this->data[$field])) {
            if ($this->data[$field] < $value) {
                $this->errors[$field] = $message ?? "Field {$field} minimal {$value}.";
            }
        }
        return $this;
    }

    /**
     * Validasi nilai maksimum
     */
    public function max($field, $value, $message = null) {
        if (isset($this->data[$field]) && is_numeric($this->data[$field])) {
            if ($this->data[$field] > $value) {
                $this->errors[$field] = $message ?? "Field {$field} maksimal {$value}.";
            }
        }
        return $this;
    }

    /**
     * Validasi kecocokan dengan field lain
     */
    public function matches($field, $otherField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$otherField])) {
            if ($this->data[$field] !== $this->data[$otherField]) {
                $this->errors[$field] = $message ?? "Field {$field} tidak cocok dengan {$otherField}.";
            }
        }
        return $this;
    }

    /**
     * Validasi dengan regex pattern
     */
    public function pattern($field, $pattern, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!preg_match($pattern, $this->data[$field])) {
                $this->errors[$field] = $message ?? "Format field {$field} tidak valid.";
            }
        }
        return $this;
    }

    /**
     * Validasi alphanumeric
     */
    public function alphanumeric($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!ctype_alnum($this->data[$field])) {
                $this->errors[$field] = $message ?? "Field {$field} hanya boleh huruf dan angka.";
            }
        }
        return $this;
    }

    /**
     * Validasi URL
     */
    public function url($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                $this->errors[$field] = $message ?? "Format URL tidak valid.";
            }
        }
        return $this;
    }

    /**
     * Validasi nilai dalam array
     */
    public function in($field, $values, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!in_array($this->data[$field], $values)) {
                $this->errors[$field] = $message ?? "Nilai field {$field} tidak valid.";
            }
        }
        return $this;
    }

    /**
     * Custom validation dengan callback
     */
    public function custom($field, $callback, $message = null) {
        if (isset($this->data[$field])) {
            if (!$callback($this->data[$field])) {
                $this->errors[$field] = $message ?? "Validasi field {$field} gagal.";
            }
        }
        return $this;
    }

    /**
     * Cek apakah validasi berhasil
     */
    public function isValid() {
        return empty($this->errors);
    }

    /**
     * Cek apakah ada error
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Dapatkan semua error
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Dapatkan error untuk field tertentu
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }

    /**
     * Dapatkan pesan error pertama
     */
    public function getFirstError() {
        return reset($this->errors) ?: null;
    }

    /**
     * Reset errors
     */
    public function reset() {
        $this->errors = [];
        return $this;
    }

    /**
     * Set data baru
     */
    public function setData($data) {
        $this->data = $data;
        $this->errors = [];
        return $this;
    }
}

/**
 * Fungsi helper untuk membuat validator
 */
function validate($data) {
    return new Validator($data);
}
