<?= $this->extend($config->viewLayout) ?>
<?= $this->section('head') ?>
<title>StrandNotes| Reset Password</title>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="login-box">
    <div class="card card-outline" style="border-top: 3px solid #074C87;">
        <div class="card-header text-center" style="padding: 1.25rem 0;">
            <h3 class="mb-0" style="color: #074C87; font-weight: 600;">Admin Password Reset</h3>
        </div>
        <div class="card-body">
            <p class="login-box-msg" style="color: #555; margin-bottom: 1.25rem;"><?= lang('Auth.forgotPasswordMessage') ?></p>
            <?= view('Modules\Auth\Views\_message_block') ?>
            <form action="<?= route_to('forgot') ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group mb-3">
                    <input type="email" 
                           class="form-control" 
                           name="email" 
                           placeholder="<?= lang('Auth.email') ?>" 
                           required
                           style="border-color: #cbd5e1;">
                    <div class="input-group-append">
                        <div class="input-group-text" style="background-color: #f8fafc; border-color: #cbd5e1;">
                            <span class="fas fa-envelope" style="color: #074C87;"></span>
                        </div>
                    </div>
                    <?php if (session('errors.email')): ?>
                        <div class="invalid-feedback d-block" style="color: #e74a3b; font-size: 0.875rem; margin-top: 0.25rem;">
                            <?= session('errors.email') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" 
                                class="btn btn-block" 
                                style="background-color: #074C87; color: white; border: none; font-weight: 500;">
                            <?= lang('Auth.resetPassword') ?>
                        </button>
                    </div>
                </div>
            </form>
            <p class="mt-3 mb-1 text-center">
                <a href="<?= route_to('backend-login') ?>" style="color: #074C87; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-arrow-left"></i> <?= lang('Auth.loginAction') ?>
                </a>
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>