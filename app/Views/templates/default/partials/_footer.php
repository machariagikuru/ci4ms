<?php
// Safely retrieve settings from cache (as done in BaseController)
$settings = cache('settings') ? (object) cache('settings') : (object) ['siteName' => 'My Site'];
?>

<footer class="bg-dark py-4 mt-auto">
    <div class="container px-5">
        <div class="row align-items-center justify-content-between flex-column flex-sm-row">
            <div class="col-auto">
                <div class="small m-0 text-white">
                    Copyright &copy; <?= esc($settings->siteName ?? 'My Site') ?> <?= date('Y') ?>
                </div>
            </div>
            <div class="col-auto">
                <a class="link-light small" href="<?= site_url('privacy') ?>">Privacy</a>
                <span class="text-white mx-1">&middot;</span>
                <a class="link-light small" href="<?= site_url('terms') ?>">Terms</a>
                <span class="text-white mx-1">&middot;</span>
                <a class="link-light small" href="<?= site_url('contact') ?>">Contact</a>
            </div>
        </div>
    </div>
</footer>