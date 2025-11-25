<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <?= $this->include('auth/_sidebar') ?>
        <div class="col-md-9 col-lg-10">
            <div class="card shadow" style="border-color: #e6f0fa;">
                <div class="card-header" style="background-color: #074C87; color: white;">
                    <h4>Edit Profile</h4>
                </div>
                <div class="card-body"">
                    <?= view('auth/_message_block') ?>
                    <form method="post" action="<?= site_url('my-account/profile') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label style="color: #074C87; font-weight: 500;">First Name</label>
                            <input type="text" name="firstname" class="form-control" value="<?= esc($user->firstname) ?>" required
                                   style="border-color: #cbd5e1; background-color: #e6f0fa;">
                        </div>
                        <div class="mb-3">
                            <label style="color: #074C87; font-weight: 500;">Last Name</label>
                            <input type="text" name="sirname" class="form-control" value="<?= esc($user->sirname) ?>" required
                                   style="border-color: #cbd5e1; background-color: #e6f0fa">
                        </div>
                        <div class="mb-3">
                            <label style="color: #074C87; font-weight: 500;">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($user->email) ?>" required
                                   style="border-color: #cbd5e1; background-color: #e6f0fa">
                        </div>
                        <button type="submit" class="btn" style="background-color: #074C87; color: white; border: none;">
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>