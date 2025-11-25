<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <?= $this->include('auth/_sidebar') ?>
        <div class="col-md-9 col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
                <div class="card-header" style="background-color: #074C87; color: white; font-weight: 600; padding: 1rem 1.25rem; border-radius: 0.5rem 0.5rem 0 0;">
                    <h4 class="mb-0" style="font-size: 1.25rem;">
                        Welcome back, <?= esc($user->firstname) ?>!
                    </h4>
                </div>
                <div class="card-body" style="padding: 1.25rem;">
                    <p class="mb-0" style="color: #555; line-height: 1.6;">
                        Manage your account, update your profile, and change your password.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>