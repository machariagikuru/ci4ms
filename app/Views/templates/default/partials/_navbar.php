<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #074C87;">
    <div class="container px-5">
        <a class="navbar-brand" href="<?= base_url() ?>" style="font-weight: 700; font-size: 1.4rem; letter-spacing: -0.5px; color: white;">
            STRANDNOTES
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php menu($menus ?? []); ?>

                <!-- Authentication Links -->
                <?php
                $authLib = new \Modules\Auth\Libraries\AuthLibrary();
                if ($authLib->isLoggedIn()):
                    $user = $authLib->getUser();
                    $displayName = $user ? esc($user->firstname ?? 'User') : 'User';
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #e9ff4e; font-weight: 500;">
                            Hello, <?= $displayName ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" style="background-color: #074C87; border-color: #1a66a3;">
                            <li><a class="dropdown-item" href="<?= base_url('my-account') ?>" style="color: #e9ff4e;;"><i class="bi bi-person"></i> My Account</a></li>
                            <li><hr class="dropdown-divider" style="border-color: #1a66a3;"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>" style="color: #ff6b6b;"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('login') ?>" style="color: #e9ff4e; font-weight: 500;"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('register') ?>" style="color: #e9ff4e; font-weight: 500;"><i class="bi bi-person-plus"></i> Register</a>
                    </li>
                <?php endif; ?>

                <!-- Search Form -->
                <li class="nav-item">
                    <form action="<?= base_url('search') ?>" method="post" class="d-flex">
                        <?= csrf_field() ?>
                        <input type="text" name="q" class="form-control me-2" placeholder="Search..." required>
                        <button class="btn btn-outline-light btn-sm" type="submit">Go</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>