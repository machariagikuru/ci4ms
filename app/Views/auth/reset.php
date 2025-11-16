<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4><?= lang('Auth.resetPassword') ?></h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>
                    <form method="post" action="<?= site_url("reset-password/$token") ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= esc($token) ?>">
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.email') ?></label>
                            <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.newPassword') ?></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.confirmPassword') ?></label>
                            <input type="password" name="pass_confirm" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100"><?= lang('Auth.resetPassword') ?></button>
                    </form>
                    <hr>
                    <a href="<?= site_url('login') ?>">&larr; <?= lang('Auth.backToLogin') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>