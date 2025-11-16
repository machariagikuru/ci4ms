<?= $this->extend($config->viewLayout) ?>
<?= $this->section('head') ?>
<title>Ci4MS - <?= getGitVersion() ?> | Şifremi Unuttum</title>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="login-box">
    <div class="card card-outline card-success">
        <div class="card-header text-center">
            <img src="<?= base_url('be-assets/img/bfo-logo.jpg') ?>" alt="" class="img-fluid">
        </div>
        <div class="card-body">
            <p class="login-box-msg"><?= lang('Auth.forgotPasswordMessage') ?></p>
            <?= view('Modules\Auth\Views\_message_block') ?>
            <form action="<?= route_to('forgot') ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" name="email" placeholder="<?= lang('Auth.email') ?>" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <?php if (session('errors.email')): ?>
                        <div class="invalid-feedback d-block">
                            <?= session('errors.email') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-block"><?= lang('Auth.resetPassword') ?></button>
                    </div>
                </div>
            </form>
            <p class="mt-3 mb-1">
                <a href="<?= route_to('backend/login') ?>"><i class="fas fa-arrow-left"></i> <?= lang('Auth.loginAction') ?></a>
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>