<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header text-center bg-primary text-white">
                    <h4><?= lang('Auth.loginAction') ?></h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>

                    <form action="<?= site_url('login') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.email') ?></label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" value="<?= old('email') ?>" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.password') ?></label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="loginPassword" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleLoginPassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                CAPTCHA: What is <?= esc($mathCaptcha) ?>?
                            </label>
                            <input type="number" name="captcha" class="form-control" placeholder="Enter the sum" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label"><?= lang('Auth.rememberMe') ?></label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><?= lang('Auth.loginAction') ?></button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <a href="<?= site_url('forgot-password') ?>"><?= lang('Auth.forgotPassword') ?></a><br>
                        <a href="<?= site_url('register') ?>"><?= lang('Auth.needAnAccount') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('toggleLoginPassword').addEventListener('click', function () {
    const pwd = document.getElementById('loginPassword');
    const icon = this.querySelector('i');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
});
</script>
<?= $this->endSection() ?>