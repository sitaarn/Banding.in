/**
 * ============================================
 * validasi.js - Validasi Form Client-Side
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Berisi:
 * 1. Class FormValidator - validasi form secara real-time (blur & submit)
 * 2. Utility: auto-dismiss alert, validasi file upload, confirm delete
 * 3. Debounce search form
 * 4. Loading indicator overlay
 * 5. Bootstrap tooltip initialization
 */

document.addEventListener('DOMContentLoaded', function() {

    // ═══════════════════════════════════════════
    //  FORM VALIDATOR CLASS
    // ═══════════════════════════════════════════

    /**
     * Class untuk validasi form secara otomatis.
     * Mendukung: required, email, minLength, maxLength, number, pattern, confirm password.
     * Validasi dilakukan saat blur (keluar field) dan saat submit.
     */
    class FormValidator {
        /**
         * @param {string} formId - ID elemen form yang akan divalidasi
         */
        constructor(formId) {
            this.form = document.getElementById(formId);
            this.errors = {}; // Menyimpan error per field

            if (this.form) {
                this.init();
            }
        }

        /** Pasang event listener submit & real-time validation */
        init() {
            // Cegah submit jika validasi gagal
            this.form.addEventListener('submit', (e) => {
                if (!this.validate()) {
                    e.preventDefault();
                }
            });

            // Validasi real-time: blur (keluar field) & input (saat mengetik)
            const inputs = this.form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                // Re-validasi saat mengetik jika field sedang error
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });
        }

        /** Validasi semua field di form. Return true jika semua valid. */
        validate() {
            this.errors = {};
            const inputs = this.form.querySelectorAll('input, select, textarea');
            let isValid = true;

            inputs.forEach(input => {
                if (!this.validateField(input)) {
                    isValid = false;
                }
            });

            return isValid;
        }

        /**
         * Validasi satu field input.
         * Cek: required, email, minLength, maxLength, number, pattern, confirm password.
         * Update UI (tambah class is-invalid/is-valid, tampilkan pesan error).
         */
        validateField(input) {
            const value = input.value.trim();
            const name = input.name;
            let isValid = true;
            let errorMessage = '';

            // Reset state sebelumnya
            input.classList.remove('is-invalid', 'is-valid');

            // Cek: required
            if (input.hasAttribute('required') && !value) {
                isValid = false;
                errorMessage = 'Field ini wajib diisi.';
            }

            // Cek: format email
            if (isValid && input.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Format email tidak valid.';
                }
            }

            // Cek: panjang minimum
            if (isValid && input.hasAttribute('minlength') && value) {
                const minLength = parseInt(input.getAttribute('minlength'));
                if (value.length < minLength) {
                    isValid = false;
                    errorMessage = `Minimal ${minLength} karakter.`;
                }
            }

            // Cek: panjang maksimum
            if (isValid && input.hasAttribute('maxlength') && value) {
                const maxLength = parseInt(input.getAttribute('maxlength'));
                if (value.length > maxLength) {
                    isValid = false;
                    errorMessage = `Maksimal ${maxLength} karakter.`;
                }
            }

            // Cek: harus angka
            if (isValid && input.type === 'number' && value) {
                if (isNaN(value)) {
                    isValid = false;
                    errorMessage = 'Harus berupa angka.';
                }
            }

            // Cek: regex pattern (dari atribut HTML)
            if (isValid && input.hasAttribute('pattern') && value) {
                const pattern = new RegExp(input.getAttribute('pattern'));
                if (!pattern.test(value)) {
                    isValid = false;
                    errorMessage = input.getAttribute('data-pattern-message') || 'Format tidak valid.';
                }
            }

            // Cek: confirm password harus cocok
            if (isValid && name === 'confirm_password' && value) {
                const password = this.form.querySelector('input[name="password"]');
                if (password && value !== password.value) {
                    isValid = false;
                    errorMessage = 'Konfirmasi password tidak cocok.';
                }
            }

            // Update UI
            if (!isValid) {
                input.classList.add('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback') ||
                                input.closest('.mb-3')?.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = errorMessage;
                }
                this.errors[name] = errorMessage;
            } else if (value) {
                input.classList.add('is-valid');
                delete this.errors[name];
            }

            return isValid;
        }

        /** Ambil semua error yang ada */
        getErrors() {
            return this.errors;
        }
    }

    // ═══════════════════════════════════════════
    //  UTILITY: Auto-Dismiss Alert
    // ═══════════════════════════════════════════

    /** Tutup semua alert secara otomatis setelah 5 detik */
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // ═══════════════════════════════════════════
    //  UTILITY: Validasi File Upload
    // ═══════════════════════════════════════════

    /** Validasi ukuran dan tipe file saat user memilih file */
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (this.files && this.files[0]) {
                const file = this.files[0];

                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                if (!allowedTypes.includes(file.type)) {
                    alert('Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF.');
                    this.value = '';
                    return;
                }
            }
        });
    });

    // ═══════════════════════════════════════════
    //  UTILITY: Confirm Delete
    // ═══════════════════════════════════════════

    /** Tampilkan dialog konfirmasi sebelum hapus (dari atribut data-confirm) */
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // ═══════════════════════════════════════════
    //  INISIALISASI FORM VALIDATORS
    // ═══════════════════════════════════════════

    // Buat validator untuk form yang ada di halaman
    if (document.getElementById('loginForm')) {
        new FormValidator('loginForm');
    }
    if (document.getElementById('registerForm')) {
        new FormValidator('registerForm');
    }
    if (document.getElementById('mahasiswaForm')) {
        new FormValidator('mahasiswaForm');
    }

    // ═══════════════════════════════════════════
    //  SEARCH FORM (Debounce)
    // ═══════════════════════════════════════════

    /** Debounce search: delay 500ms setelah user berhenti mengetik */
    const searchForm = document.querySelector('form[action*="mahasiswa"]');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        if (searchInput) {
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    // Optional: Auto-submit setelah selesai mengetik
                    // searchForm.submit();
                }, 500);
            });
        }
    }

    // ═══════════════════════════════════════════
    //  LOADING INDICATOR
    // ═══════════════════════════════════════════

    /** Tampilkan overlay loading spinner (spinner Bootstrap) */
    window.showLoading = function() {
        const overlay = document.createElement('div');
        overlay.className = 'spinner-overlay';
        overlay.id = 'loadingOverlay';
        overlay.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;
        document.body.appendChild(overlay);
    };

    /** Hapus overlay loading spinner */
    window.hideLoading = function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    };

    // ═══════════════════════════════════════════
    //  TOOLTIPS (Bootstrap)
    // ═══════════════════════════════════════════

    /** Inisialisasi semua Bootstrap tooltip di halaman */
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

});
