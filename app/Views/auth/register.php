<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #e6f0fa;">
                <div class="card-header text-center" style="background-color: #074C87; color: white; font-weight: 600; padding: 1.25rem; border-radius: 0.5rem 0.5rem 0 0;">
                    <h4 class="mb-0" style="font-size: 1.25rem;"><?= lang('Auth.register') ?></h4>
                </div>
                <div class="card-body" style="padding: 1.5rem;">
                    <?= view('auth/_message_block') ?>

                    <form action="<?= site_url('register') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: #074C87;"><?= lang('First Name') ?></label>
                            <input type="text" 
                                   name="firstname" 
                                   class="form-control" 
                                   value="<?= old('firstname') ?>" 
                                   required
                                   style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: #074C87;"><?= lang('Last Name') ?></label>
                            <input type="text" 
                                   name="sirname" 
                                   class="form-control" 
                                   value="<?= old('sirname') ?>" 
                                   required
                                   style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: #074C87;"><?= lang('Auth.email') ?></label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?= old('email') ?>" 
                                   required
                                   style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: #074C87;"><?= lang('Auth.password') ?></label>
                            <div class="input-group">
                                <input type="password" 
                                       name="password" 
                                       class="form-control" 
                                       id="registerPassword" 
                                       placeholder="••••••••" 
                                       required
                                       style="border: 1px solid #cbd5e1; border-radius: 0.375rem 0 0 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                                <button class="btn" 
                                        type="button" 
                                        id="toggleRegisterPassword"
                                        style="background-color: #e6f0fa; border: 1px solid #cbd5e1; border-left: none; border-radius: 0 0.375rem 0.375rem 0; width: 44px;">
                                    <i class="bi bi-eye" style="color: #074C87;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium" style="color: #074C87;">
                                CAPTCHA: What is <?= esc($mathCaptcha) ?>?
                            </label>
                            <input type="number" 
                                   name="captcha" 
                                   class="form-control" 
                                   placeholder="Enter the sum" 
                                   required
                                   style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                        </div>

                        <button type="submit" 
                                class="btn w-100"
                                style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.625rem 1rem; font-size: 0.95rem;">
                            <?= lang('Register') ?>
                        </button>
                    </form>

                    <hr style="border-color: #e6f0fa; margin: 1.25rem 0;">

                    <div class="text-center">
                        <a href="<?= site_url('login') ?>" 
                           style="color: #074C87; text-decoration: none; font-weight: 500;">
                            <?= lang('Already Registered? Log In') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('toggleRegisterPassword').addEventListener('click', function () {
    const pwd = document.getElementById('registerPassword');
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