<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h4><?= lang('Auth.forgotPassword') ?></h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>
                    <form method="post" action="<?= site_url('forgot-password') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label"><?= lang('Auth.email') ?></label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><?= lang('Auth.sendResetLink') ?></button>
                    </form>
                    <hr>
                    <a href="<?= site_url('login') ?>">&larr; <?= lang('Auth.backToLogin') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>