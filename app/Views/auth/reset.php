<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow" style="border-color: #e6f0fa;">
                <div class="card-header text-center" style="background-color: #074C87; color: white;">
                    <h4><?= lang('Auth.resetPassword') ?></h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>
                    <form method="post" action="<?= site_url("reset-password/$token") ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="token" value="<?= esc($token) ?>">
                        <div class="mb-3">
                            <label class="form-label" style="color: #074C87; font-weight: 500;"><?= lang('Auth.email') ?></label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?= old('email') ?>" 
                                   required
                                   style="border-color: #cbd5e1;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="color: #074C87; font-weight: 500;"><?= lang('Auth.newPassword') ?></label>
                            <input type="password" 
                                   name="password" 
                                   class="form-control" 
                                   required
                                   style="border-color: #cbd5e1;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="color: #074C87; font-weight: 500;"><?= lang('Auth.confirmPassword') ?></label>
                            <input type="password" 
                                   name="pass_confirm" 
                                   class="form-control" 
                                   required
                                   style="border-color: #cbd5e1;">
                        </div>
                        <button type="submit" 
                                class="btn w-100" 
                                style="background-color: #074C87; color: white; border: none; font-weight: 500;">
                            <?= lang('Auth.resetPassword') ?>
                        </button>
                    </form>
                    <hr style="border-color: #e6f0fa;">
                    <a href="<?= site_url('login') ?>" style="color: #074C87; text-decoration: none; font-weight: 500;">
                        &larr; <?= lang('Auth.backToLogin') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>