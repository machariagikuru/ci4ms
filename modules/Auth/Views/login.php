<?= $this->extend($config->viewLayout) ?>
<?= $this->section('head') ?>
<title>Ci4MS - <?= getGitVersion() ?> | Giriş Yap</title>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="login-box">
    <div class="card card-outline card-success">
        <div class="card-header text-center">
            <img src="<?= base_url('be-assets/img/bfo-logo.jpg') ?>" alt="" class="img-fluid">
        </div>
        <div class="card-body">
            <p class="login-box-msg"><?= lang('Auth.loginMessage') ?></p>
            <?= view('Modules\Auth\Views\_message_block') ?>
            <form action="<?= route_to('login') ?>" method="post">
                <?= csrf_field() ?>

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" id="adminPassword" required placeholder="<?= lang('Auth.password') ?>">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <span class="fas fa-eye"></span>
                        </button>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            CAPTCHA: What is <?= esc($mathCaptcha) ?>?
                        </span>
                    </div>
                    <input type="number" name="captcha" class="form-control" required placeholder="Enter the sum">
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="icheck-success">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">
                                <?= lang('Auth.rememberMe') ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success btn-block"><?= lang('Auth.loginAction') ?></button>
                    </div>
                </div>
            </form>
            <hr>
            <p class="mb-1">
                <a href="<?= route_to('backend/forgot') ?>"><?= lang('Auth.forgotPassword') ?></a>
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