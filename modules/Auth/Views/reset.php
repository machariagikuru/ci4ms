<?= $this->extend($config->viewLayout) ?>
<?= $this->section('head') ?>
<title>Ci4MS - <?= getGitVersion() ?> | Şifremi Alma</title>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="login-box">
    <div class="card card-outline" style="border-top: 3px solid #074C87;">
        <div class="card-header text-center" style="padding: 1.25rem 0;">
            <h3 class="mb-0" style="color: #074C87; font-weight: 600;">Admin Login</h3>
        </div>
        <div class="card-body">
            <p class="login-box-msg" style="color: #555; margin-bottom: 1.25rem;"><?= lang('Auth.generatePasswordMessage') ?></p>
            <?= view('Modules\Auth\Views\_message_block') ?>

            <form action="<?= route_to('reset-password', $token) ?>" method="post">
                <?= csrf_field() ?>

                <!-- Email -->
                <div class="form-group mb-3">
                    <label for="email" style="color: #074C87; font-weight: 500; display: block; margin-bottom: 0.5rem;">
                        <?= lang('Auth.email') ?>
                    </label>
                    <input type="email" 
                           class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                           name="email" 
                           placeholder="<?= lang('Auth.email') ?>" 
                           value="<?= old('email') ?>" 
                           required
                           style="border-color: <?= session('errors.email') ? '#e74a3b' : '#cbd5e1' ?>;">
                    <?php if (session('errors.email')): ?>
                        <div class="invalid-feedback d-block" style="color: #e74a3b; font-size: 0.875rem; margin-top: 0.25rem;">
                            <?= session('errors.email') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- New Password -->
                <div class="form-group mb-3">
                    <label for="password" style="color: #074C87; font-weight: 500; display: block; margin-bottom: 0.5rem;">
                        <?= lang('Auth.newPassword') ?>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                               name="password" 
                               id="resetPassword" 
                               required
                               style="border-color: <?= session('errors.password') ? '#e74a3b' : '#cbd5e1' ?>; border-radius: 0.375rem 0 0 0.375rem;">
                        <button class="btn" 
                                type="button" 
                                id="toggleResetPassword"
                                style="background-color: #f8fafc; border: 1px solid <?= session('errors.password') ? '#e74a3b' : '#cbd5e1' ?>; border-left: none; border-radius: 0 0.375rem 0.375rem 0; width: 44px;">
                            <span class="fas fa-eye" style="color: #074C87;"></span>
                        </button>
                    </div>
                    <?php if (session('errors.password')): ?>
                        <div class="invalid-feedback d-block" style="color: #e74a3b; font-size: 0.875rem; margin-top: 0.25rem;">
                            <?= session('errors.password') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div class="form-group mb-3">
                    <label for="pass_confirm" style="color: #074C87; font-weight: 500; display: block; margin-bottom: 0.5rem;">
                        <?= lang('Auth.newPasswordRepeat') ?>
                    </label>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control <?= session('errors.pass_confirm') ? 'is-invalid' : '' ?>"
                               name="pass_confirm" 
                               id="resetPasswordConfirm" 
                               required
                               style="border-color: <?= session('errors.pass_confirm') ? '#e74a3b' : '#cbd5e1' ?>; border-radius: 0.375rem 0 0 0.375rem;">
                        <button class="btn" 
                                type="button" 
                                id="toggleResetPasswordConfirm"
                                style="background-color: #f8fafc; border: 1px solid <?= session('errors.pass_confirm') ? '#e74a3b' : '#cbd5e1' ?>; border-left: none; border-radius: 0 0.375rem 0.375rem 0; width: 44px;">
                            <span class="fas fa-eye" style="color: #074C87;"></span>
                        </button>
                    </div>
                    <?php if (session('errors.pass_confirm')): ?>
                        <div class="invalid-feedback d-block" style="color: #e74a3b; font-size: 0.875rem; margin-top: 0.25rem;">
                            <?= session('errors.pass_confirm') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="btn btn-block" 
                        style="background-color: #074C87; color: white; border: none; font-weight: 500; padding: 0.625rem 1rem; border-radius: 0.375rem;">
                    <?= lang('Auth.resetPassword') ?>
                </button>
            </form>

            <p class="mt-3 mb-1 text-center">
                <a href="<?= route_to('backend/login') ?>" style="color: #074C87; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-arrow-left"></i> <?= lang('Auth.loginAction') ?>
                </a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('toggleResetPassword').addEventListener('click', function () {
    const pwd = document.getElementById('resetPassword');
    const icon = this.querySelector('span');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'fas fa-eye';
    }
});

document.getElementById('toggleResetPasswordConfirm').addEventListener('click', function () {
    const pwd = document.getElementById('resetPasswordConfirm');
    const icon = this.querySelector('span');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'fas fa-eye';
    }
});
</script>
<?= $this->endSection() ?>