<!-- Header -->
<header class="py-5" style="background-color: #074C87;">
    <div class="container px-5">
        <div class="row gx-5 align-items-center justify-content-center">
            <div class="col-lg-10 col-xl-9 col-xxl-8 text-center">
                <div class="my-5">
                    <h1 class="display-5 fw-bolder text-white mb-2">StrandNotes</h1>
                    <p class="lead fw-normal text-white-50 mb-4">
                        Quickly create, share, and access competency-based curriculum notes anytime, anywhere!
                    </p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-center">
                        <?php
                        $authLib = new \Modules\Auth\Libraries\AuthLibrary();
                        if ($authLib->isLoggedIn()): ?>
                            <a class="btn btn-warning btn-lg px-4 me-sm-3" href="<?= base_url('dashboard') ?>" style="background-color: #e9ff4e; color: #074C87; font-weight: 600; border: none;">
                                Dashboard
                            </a>
                            <a class="btn btn-outline-light btn-lg px-4" href="#features">
                                Learn More
                            </a>
                        <?php else: ?>
                            <a class="btn btn-warning btn-lg px-4 me-sm-3" href="<?= base_url('register') ?>" style="background-color: #e9ff4e; color: #074C87; font-weight: 600; border: none;">
                                Get Started
                            </a>
                            <a class="btn btn-outline-light btn-lg px-4" href="<?= base_url('login') ?>">
                                Log In
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>