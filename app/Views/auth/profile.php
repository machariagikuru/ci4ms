<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <?= $this->include('auth/_sidebar') ?>
        <div class="col-md-9 col-lg-10">
            <div class="card shadow">
                <div class="card-header">
                    <h4>Edit Profile</h4>
                </div>
                <div class="card-body">
                    <?= view('auth/_message_block') ?>
                    <form method="post" action="<?= site_url('my-account/profile') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" name="firstname" class="form-control" value="<?= esc($user->firstname) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" name="sirname" class="form-control" value="<?= esc($user->sirname) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($user->email) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>