<?= $this->extend($config->viewLayout) ?>
<?= $this->section('head') ?>
<title>StrandNotes - | Log in</title>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="login-box">
    <div class="card card-outline" style="border-top: 3px solid #074C87;">
        <div class="card-header text-center" style="padding: 1.25rem 0;">
            <h3 class="mb-0" style="color: #074C87; font-weight: 600;">Admin Login</h3>
        </div>
        <div class="card-body">
            <p class="login-box-msg" style="color: #555; margin-bottom: 1.25rem;"><?= lang('Auth.loginMessage') ?></p>
            <?= view('Modules\Auth\Views\_message_block') ?>

            <form action="<?= base_url('backend/login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="input-group mb-3">
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Email" 
                           required 
                           autofocus
                           style="border-color: #cbd5e1;">
                    <div class="input-group-append">
                        <div class="input-group-text" style="background-color: #f8fafc; border-color: #cbd5e1;">
                            <span class="fas fa-envelope" style="color: #074C87;"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" 
                           name="password" 
                           class="form-control" 
                           id="adminPassword" 
                           required 
                           placeholder="<?= lang('Auth.password') ?>"
                           style="border-color: #cbd5e1;">
                    <div class="input-group-append">
                        <button class="btn" 
                                type="button" 
                                id="togglePassword"
                                style="background-color: #f8fafc; border-color: #cbd5e1; border-left: none;">
                            <span class="fas fa-eye" style="color: #074C87;"></span>
                        </button>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="background-color: #f8fafc; border-color: #cbd5e1; color: #074C87; font-weight: 500;">
                            CAPTCHA: What is <?= esc($mathCaptcha) ?>?
                        </span>
                    </div>
                    <input type="number" 
                           name="captcha" 
                           class="form-control" 
                           required 
                           placeholder="Enter the sum"
                           style="border-color: #cbd5e1;">
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="icheck-success">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember" style="color: #555; font-weight: normal;">
                                <?= lang('Auth.rememberMe') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" 
                                class="btn btn-block" 
                                style="background-color: #074C87; color: white; border: none; font-weight: 500;">
                            <?= lang('Auth.loginAction') ?>
                        </button>
                    </div>
                </div>
            </form>

            <hr style="border-color: #e6f0fa; margin: 1.25rem 0;">

            <p class="mb-1 text-center">
                <a href="<?= base_url('backend/forgot') ?>" style="color: #074C87; text-decoration: none; font-weight: 500;">
                    <?= lang('Auth.forgotPassword') ?>
                </a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const pwd = document.getElementById('adminPassword');
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