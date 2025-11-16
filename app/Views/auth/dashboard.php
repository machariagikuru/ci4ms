<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <?= $this->include('auth/_sidebar') ?>
        <div class="col-md-9 col-lg-10">
            <div class="card shadow">
                <div class="card-header">
                    <h4>Welcome back, <?= esc($user->firstname) ?>!</h4>
                </div>
                <div class="card-body">
                    <p>Manage your account, update your profile, and change your password.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>