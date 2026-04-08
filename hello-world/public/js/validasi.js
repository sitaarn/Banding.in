/**
 * =====================================================
 * JAVASCRIPT VALIDATION
 * Praktikum Aplikasi Web - Universitas Tidar
 * =====================================================
 */

document.addEventListener('DOMContentLoaded', function() {

    // =====================================================
    // FORM VALIDATION CLASS
    // =====================================================
    class FormValidator {
        constructor(formId) {
            this.form = document.getElementById(formId);
            this.errors = {};

            if (this.form) {
                this.init();
            }
        }

        init() {
            this.form.addEventListener('submit', (e) => {
                if (!this.validate()) {
                    e.preventDefault();
                }
            });

            // Real-time validation on blur
            const inputs = this.form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });

                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input);
                    }
                });
            });
        }

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

        validateField(input) {
            const value = input.value.trim();
            const name = input.name;
            let isValid = true;
            let errorMessage = '';

            // Remove previous error state
            input.classList.remove('is-invalid', 'is-valid');

            // Required validation
            if (input.hasAttribute('required') && !value) {
                isValid = false;
                errorMessage = 'Field ini wajib diisi.';
            }

            // Email validation
            if (isValid && input.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Format email tidak valid.';
                }
            }

            // MinLength validation
            if (isValid && input.hasAttribute('minlength') && value) {
                const minLength = parseInt(input.getAttribute('minlength'));
                if (value.length < minLength) {
                    isValid = false;
                    errorMessage = `Minimal ${minLength} karakter.`;
                }
            }

            // MaxLength validation
            if (isValid && input.hasAttribute('maxlength') && value) {
                const maxLength = parseInt(input.getAttribute('maxlength'));
                if (value.length > maxLength) {
                    isValid = false;
                    errorMessage = `Maksimal ${maxLength} karakter.`;
                }
            }

            // Number validation
            if (isValid && input.type === 'number' && value) {
                if (isNaN(value)) {
                    isValid = false;
                    errorMessage = 'Harus berupa angka.';
                }
            }

            // Pattern validation
            if (isValid && input.hasAttribute('pattern') && value) {
                const pattern = new RegExp(input.getAttribute('pattern'));
                if (!pattern.test(value)) {
                    isValid = false;
                    errorMessage = input.getAttribute('data-pattern-message') || 'Format tidak valid.';
                }
            }

            // Password confirmation
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

        getErrors() {
            return this.errors;
        }
    }

    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // File size validation
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (this.files && this.files[0]) {
                const file = this.files[0];

                // Check file size
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipe file tidak diizinkan. Gunakan JPG, PNG, atau GIF.');
                    this.value = '';
                    return;
                }
            }
        });
    });

    // Confirm delete
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Initialize form validators
    if (document.getElementById('loginForm')) {
        new FormValidator('loginForm');
    }

    if (document.getElementById('registerForm')) {
        new FormValidator('registerForm');
    }

    if (document.getElementById('mahasiswaForm')) {
        new FormValidator('mahasiswaForm');
    }

    // =====================================================
    // SEARCH FORM
    // =====================================================
    const searchForm = document.querySelector('form[action*="mahasiswa"]');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        if (searchInput) {
            // Debounce search
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    // Optional: Auto-submit after typing
                    // searchForm.submit();
                }, 500);
            });
        }
    }

    // =====================================================
    // LOADING INDICATOR
    // =====================================================
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

    window.hideLoading = function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    };

    // =====================================================
    // TOOLTIPS
    // =====================================================
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

});
