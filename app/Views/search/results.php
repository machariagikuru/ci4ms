<?= $this->extend('templates/default/base') ?>
<?= $this->section('content') ?>
<div class="container py-5">
    <h2>Search Results for "<?= esc($keyword) ?>"</h2>

    <?php if (empty($blogs) && empty($pages)): ?>
        <div class="alert alert-info">No results found.</div>
    <?php else: ?>
        <?php if (!empty($blogs)): ?>
            <h3>Blogs (<?= count($blogs) ?>)</h3>
            <?php foreach ($blogs as $blog): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?= site_url('blog/' . $blog->seflink) ?>">
                                <?= esc($blog->title) ?>
                            </a>
                        </h5>
                        <p class="card-text"><?= esc(strip_tags($blog->excerpt)) ?>...</p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($pages)): ?>
            <h3>Pages (<?= count($pages) ?>)</h3>
            <?php foreach ($pages as $page): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="<?= site_url($page->seflink) ?>">
                                <?= esc($page->title) ?>
                            </a>
                        </h5>
                        <p class="card-text"><?= esc(strip_tags($blog->excerpt ?? '')) ?>...</p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>