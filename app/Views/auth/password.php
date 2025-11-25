<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <?= $this->include('auth/_sidebar') ?>
        <div class="col-md-9 col-lg-10">
            <div class="card shadow" style="border-color: #e6f0fa;">
                <div class="card-header" style="background-color: #074C87; color: white;">
                    <h4>Change Password</h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>
                    <form method="post" action="<?= site_url('my-account/password') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label style="color: #074C87; font-weight: 500;">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required
                                   style="border-color: #cbd5e1; background-color: #e6f0fa;">
                        </div>
                        <div class="mb-3">
                            <label style="color: #074C87; font-weight: 500;">New Password</label>
                            <input type="password" name="new_password" class="form-control" required
                                   style="border-color: #cbd5e1; background-color: #e6f0fa;">
                        </div>
                        <div class="mb-3">
                            <label style="color: #074C87; font-weight: 500;">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required
                                   style="border-color: #cbd5e1; background-color: #e6f0fa;">
                        </div>
                        <button type="submit" class="btn" style="background-color: #074C87; color: white; border: none;">
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>