<?php
$settings = cache('settings') ? (object) cache('settings') : (object) ['siteName' => 'My Site'];
?>

<!-- Subtle but clear dividing line -->
<hr class="my-0" style="border-top: 2px solid #e9ff4e; opacity: 1;">

<footer class="py-4" style="background-color: #074C87;">
    <div class="container px-5">
        <div class="row align-items-center justify-content-between flex-column flex-sm-row">
            <div class="col-auto mb-3 mb-sm-0 text-center text-sm-start">
                <div class="small" style="color: rgba(255,255,255,0.85); font-weight: 500;">
                    Copyright &copy; <?= esc($settings->siteName ?? 'My Site') ?> <?= date('Y') ?>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a class="text-decoration-none small fw-semibold" 
                       href="<?= site_url('privacy') ?>" 
                       style="color: #e9ff4e; padding: 0.25rem 0.5rem; border-radius: 4px; background-color: rgba(233, 255, 78, 0.1);">
                        Privacy
                    </a>
                    <a class="text-decoration-none small fw-semibold" 
                       href="<?= site_url('terms') ?>" 
                       style="color: #e9ff4e; padding: 0.25rem 0.5rem; border-radius: 4px; background-color: rgba(233, 255, 78, 0.1);">
                        Terms
                    </a>
                    <a class="text-decoration-none small fw-semibold" 
                       href="<?= site_url('contact') ?>" 
                       style="color: #e9ff4e; padding: 0.25rem 0.5rem; border-radius: 4px; background-color: rgba(233, 255, 78, 0.1);">
                        Contact
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>