<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header text-center">
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
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>

                        <!-- Optional: CAPTCHA (like admin) -->
                        <?php if (isset($cap)): ?>
                        <div class="mb-3">
                            <img src="<?= $cap->inline() ?>" alt="CAPTCHA" class="img-fluid mb-2">
                            <input type="text" name="captcha" class="form-control" placeholder="Doğrulama kodu" required>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input">
                            <label for="remember" class="form-check-label"><?= lang('Auth.rememberMe') ?></label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><?= lang('Auth.loginAction') ?></button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a href="<?= site_url('forgot-password') ?>"><?= lang('Auth.forgotPassword') ?></a>
                        <br>
                        <a href="<?= site_url('register') ?>"><?= lang('Auth.needAnAccount') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>