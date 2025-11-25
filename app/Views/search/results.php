<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <h2 class="fw-bold mb-4" style="color: #074C87;">
        Search Results for "<?= esc($keyword) ?>"
    </h2>

    <?php if (empty($blogs) && empty($pages)): ?>
        <div class="alert alert-light border" role="alert" style="background-color: #f8fafc; color: #074C87; border-color: #e6f0fa;">
            No results found.
        </div>
    <?php else: ?>
        <?php if (!empty($blogs)): ?>
            <h3 class="fw-bold mt-5 mb-3" style="color: #074C87;">
                Blogs (<?= count($blogs) ?>)
            </h3>
            <?php foreach ($blogs as $blog): ?>
                <div class="card mb-3 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            <a href="<?= site_url('blog/' . esc($blog->seflink)) ?>" 
                               class="text-decoration-none" 
                               style="color: #074C87; font-weight: 600;">
                                <?= esc($blog->title) ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted" style="color: #555 !important; line-height: 1.5;">
                            <?= esc(strip_tags($blog->excerpt)) ?>...
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($pages)): ?>
            <h3 class="fw-bold mt-5 mb-3" style="color: #074C87;">
                Pages (<?= count($pages) ?>)
            </h3>
            <?php foreach ($pages as $page): ?>
                <div class="card mb-3 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            <a href="<?= site_url(esc($page->seflink)) ?>" 
                               class="text-decoration-none" 
                               style="color: #074C87; font-weight: 600;">
                                <?= esc($page->title) ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted" style="color: #555 !important; line-height: 1.5;">
                            <?= esc(strip_tags($page->content ?? '')) ?>...
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>