<?= $this->extend($config->viewLayout) ?>
<?= $this->section('head') ?>
<title>Ci4MS - <?= getGitVersion() ?> | Şifremi Alma</title>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="login-box">
    <div class="card card-outline card-success">
        <div class="card-header text-center">
            <img src="<?= base_url('be-assets/img/bfo-logo.jpg') ?>" alt="" class="img-fluid">
        </div>
        <div class="card-body">
            <p class="login-box-msg"><?= lang('Auth.generatePasswordMessage') ?></p>
            <?= view('Modules\Auth\Views\_message_block') ?>
            <form action="<?= route_to('reset-password', $token) ?>" method="post">
                <?= csrf_field() ?>

                <div class="form-group mb-3">
                    <label for="email"><?= lang('Auth.email') ?></label>
                    <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                        name="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required>
                    <?php if (session('errors.email')): ?>
                        <div class="invalid-feedback"><?= session('errors.email') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="password"><?= lang('Auth.newPassword') ?></label>
                    <div class="input-group">
                        <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                            name="password" id="resetPassword" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggleResetPassword">
                            <span class="fas fa-eye"></span>
                        </button>
                    </div>
                    <?php if (session('errors.password')): ?>
                        <div class="invalid-feedback"><?= session('errors.password') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group mb-3">
                    <label for="pass_confirm"><?= lang('Auth.newPasswordRepeat') ?></label>
                    <div class="input-group">
                        <input type="password" class="form-control <?= session('errors.pass_confirm') ? 'is-invalid' : '' ?>"
                            name="pass_confirm" id="resetPasswordConfirm" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggleResetPasswordConfirm">
                            <span class="fas fa-eye"></span>
                        </button>
                    </div>
                    <?php if (session('errors.pass_confirm')): ?>
                        <div class="invalid-feedback"><?= session('errors.pass_confirm') ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-success btn-block"><?= lang('Auth.resetPassword') ?></button>
            </form>

            <p class="mt-3 mb-1">
                <a href="<?= route_to('backend/login') ?>"><i class="fas fa-arrow-left"></i> <?= lang('Auth.loginAction') ?></a>
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