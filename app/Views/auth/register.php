<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4><?= lang('Auth.register') ?></h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>
                    <form action="<?= site_url('register') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Backend.firstName') ?></label>
                            <input type="text" name="firstname" class="form-control" value="<?= old('firstname') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Backend.lastName') ?></label>
                            <input type="text" name="sirname" class="form-control" value="<?= old('sirname') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.email') ?></label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.password') ?></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <!-- Optional CAPTCHA -->
                        <?php if (isset($cap)): ?>
                        <div class="mb-3">
                            <img src="<?= $cap->inline() ?>" alt="CAPTCHA" class="img-fluid mb-2">
                            <input type="text" name="captcha" class="form-control" placeholder="Doğrulama kodu" required>
                        </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-success w-100"><?= lang('Auth.registerAction') ?></button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a href="<?= site_url('login') ?>"><?= lang('Auth.alreadyRegistered') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>