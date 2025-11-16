<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <?= $this->include('auth/_sidebar') ?>
        <div class="col-md-9 col-lg-10">
            <div class="card shadow">
                <div class="card-header">
                    <h4>Change Password</h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>
                    <form method="post" action="<?= site_url('my-account/password') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>