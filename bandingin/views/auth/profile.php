<div class="row justify-content-center">
    <div class="col-lg-8">
        <h2 class="mb-4">
            <i class="bi bi-person-circle me-2"></i>Profil Saya
        </h2>

        <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= e($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Update Profile -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-pencil-square me-2"></i>Update Profil
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php?page=profile">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text"
                                       class="form-control"
                                       id="username"
                                       value="<?= e($user['username']) ?>"
                                       disabled>
                                <small class="text-muted">Username tidak dapat diubah.</small>
                            </div>

                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>"
                                       id="nama_lengkap"
                                       name="nama_lengkap"
                                       value="<?= e($user['nama_lengkap']) ?>"
                                       required>
                                <?php if (isset($errors['nama_lengkap'])): ?>
                                <div class="invalid-feedback"><?= e($errors['nama_lengkap']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                       id="email"
                                       name="email"
                                       value="<?= e($user['email']) ?>"
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= e($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <input type="text"
                                       class="form-control"
                                       value="<?= e(ucfirst($user['role'])) ?>"
                                       disabled>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark">
                        <i class="bi bi-key me-2"></i>Ubah Password
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php?page=profile">
                            <input type="hidden" name="action" value="change_password">

                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    Password Saat Ini <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>"
                                       id="current_password"
                                       name="current_password"
                                       required>
                                <?php if (isset($errors['current_password'])): ?>
                                <div class="invalid-feedback"><?= e($errors['current_password']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">
                                    Password Baru <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>"
                                       id="new_password"
                                       name="new_password"
                                       required>
                                <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback"><?= e($errors['new_password']) ?></div>
                                <?php endif; ?>
                                <small class="text-muted">Minimal 6 karakter.</small>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    Konfirmasi Password Baru <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                       id="confirm_password"
                                       name="confirm_password"
                                       required>
                                <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?= e($errors['confirm_password']) ?></div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key me-2"></i>Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <i class="bi bi-info-circle me-2"></i>Informasi Akun
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Terdaftar pada:</strong><br>
                        <?= date('d F Y, H:i', strtotime($user['created_at'])) ?> WIB</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Terakhir diperbarui:</strong><br>
                        <?= date('d F Y, H:i', strtotime($user['updated_at'])) ?> WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>