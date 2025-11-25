<!-- Desktop Sidebar -->
<div class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="userSidebar" style="background-color: #f8fafc; border-right: 1px solid #e6f0fa;">
    <div class="position-sticky pt-3 pb-4" style="top: 70px;">
        <ul class="nav flex-column" style="gap: 0.375rem;">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center rounded <?= uri_string() == 'my-account' ? 'active' : '' ?>" 
                   href="<?= site_url('my-account') ?>"
                   style="padding: 0.625rem 1rem; font-weight: 500; color: #074C87; background-color: <?= uri_string() == 'my-account' ? '#e6f0fa' : 'transparent' ?>;">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center rounded <?= strpos(uri_string(), 'profile') !== false ? 'active' : '' ?>" 
                   href="<?= site_url('my-account/profile') ?>"
                   style="padding: 0.625rem 1rem; font-weight: 500; color: #074C87; background-color: <?= strpos(uri_string(), 'profile') !== false ? '#e6f0fa' : 'transparent' ?>;">
                    <i class="fas fa-user me-2"></i> Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center rounded <?= strpos(uri_string(), 'password') !== false ? 'active' : '' ?>" 
                   href="<?= site_url('my-account/password') ?>"
                   style="padding: 0.625rem 1rem; font-weight: 500; color: #074C87; background-color: <?= strpos(uri_string(), 'password') !== false ? '#e6f0fa' : 'transparent' ?>;">
                    <i class="fas fa-lock me-2"></i> Password
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center rounded" 
                   href="<?= site_url('logout') ?>"
                   style="padding: 0.625rem 1rem; font-weight: 500; color: #e74a3b; background-color: transparent;">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Mobile Toggle Button -->
<button class="btn d-md-none mb-3" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#userSidebar"
        style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1rem;">
    Menu
</button>