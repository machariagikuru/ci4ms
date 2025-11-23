<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-5">
        <a class="navbar-brand" href="<?= base_url() ?>">
            <?php if (empty($settings->logo)): ?>
                <?= esc($settings->siteName ?? 'Site') ?>
            <?php else: ?>
                <img src="<?= esc($settings->logo) ?>" alt="<?= esc($settings->siteName ?? 'Site') ?>" class="img-fluid">
            <?php endif; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php menu($menus ?? []); ?>

                <!-- Authentication Buttons -->
                <?php
                $authLib = new \Modules\Auth\Libraries\AuthLibrary();
                if ($authLib->isLoggedIn()):
                    $user = $authLib->getUser();
                    $displayName = $user ? esc($user->firstname ?? 'User') : 'User';
                    $groupId = session('group_id');
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Hello, <?= $displayName ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <?php if ($groupId == 2): ?>
                                <li><a class="dropdown-item" href="<?= base_url('my-account') ?>"><i class="bi bi-person"></i> My Account</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('login') ?>"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('register') ?>"><i class="bi bi-person-plus"></i> Register</a>
                    </li>
                <?php endif; ?>

                <!-- Search Button (wrapped in <li>) -->
                <li class="nav-item">
                    <form action="<?= base_url('search') ?>" method="post" class="d-flex">
                        <?= csrf_field() ?>
                        <input type="text" name="q" class="form-control me-2" placeholder="Search..." required>
                        <button class="btn btn-outline-light" type="submit">Go</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>