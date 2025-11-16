<div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" id="userSidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= uri_string() == 'my-account' ? 'active' : '' ?>" href="<?= site_url('my-account') ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos(uri_string(), 'profile') !== false ? 'active' : '' ?>" href="<?= site_url('my-account/profile') ?>">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= strpos(uri_string(), 'password') !== false ? 'active' : '' ?>" href="<?= site_url('my-account/password') ?>">
                    <i class="fas fa-lock"></i> Password
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= site_url('logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>
<button class="btn btn-outline-primary d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#userSidebar">
    Menu
</button>