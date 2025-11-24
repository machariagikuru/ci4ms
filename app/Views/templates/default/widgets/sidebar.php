<?php
// This file is INCLUDED inside a .col-lg-4 — do NOT add another column!
?>

<?php if (!empty($settings->templateInfos->widgets['sidebar']['searchWidget']) && (boolean)$settings->templateInfos->widgets['sidebar']['searchWidget'] === true): ?>
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.5rem; background-color: #e6f0fa;">
        <div class="card-header" style="background-color: #074C87; color: #e9ff4e;; font-weight: 600; padding: 0.75rem 1rem;">
            Search Notes
        </div>
        <div class="card-body p-3">
            <form action="<?= site_url('search') ?>" method="post">
                <?= csrf_field() ?>
                <div class="input-group">
                    <input type="text"
                           name="q"
                           class="form-control"
                           placeholder="Search notes..."
                           required
                           style="border: 1px solid #cbd5e1; border-radius: 0.375rem 0 0 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                    <button class="btn"
                            type="submit"
                            style="background-color: #074C87; color: #e9ff4e;; border: none; border-radius: 0 0.375rem 0.375rem 0; padding: 0.5rem 1rem; font-weight: 500; font-size: 0.95rem;">
                        Go
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($settings->templateInfos->widgets['sidebar']['categoriesWidget']) && (boolean)$settings->templateInfos->widgets['sidebar']['categoriesWidget'] === true): ?>
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.5rem; background-color: #fff;">
        <div class="card-header" style="background-color: #074C87; color: #e9ff4e; font-weight: 600; padding: 0.75rem 1rem;">
            Categories
        </div>
        <div class="card-body p-0;">
            <ul class="list-group list-group-flush">
                <?php if (!empty($categories) && is_array($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <li class="list-group-item border-0 py-2 px-3">
                            <a href="<?= site_url('category/' . esc($cat->seflink)) ?>"
                               class="text-decoration-none"
                               style="color: #074C87; font-weight: 500; font-size: 0.95rem;">
                                <?= esc($cat->title) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item border-0 py-2 px-3 text-muted" style="font-size: 0.9rem;">
                        No categories available.
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($settings->templateInfos->widgets['sidebar']['archiveWidget']) && (boolean)$settings->templateInfos->widgets['sidebar']['archiveWidget'] === true): ?>
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 0.5rem; background-color: #fff;">
        <div class="card-header" style="background-color: #074C87; color: white; font-weight: 600; padding: 0.75rem 1rem;">
            Archives
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php if (!empty($archives) && is_array($archives)): ?>
                    <?php foreach ($archives as $archive): ?>
                        <li class="list-group-item border-0 py-2 px-3">
                            <a href="<?= site_url('archive/' . esc($archive['seflink'] ?? '')) ?>"
                               class="text-decoration-none"
                               style="color: #074C87; font-weight: 500; font-size: 0.95rem;">
                                <?= esc($archive['label'] ?? '') ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item border-0 py-2 px-3 text-muted" style="font-size: 0.9rem;">
                        No archives available.
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>