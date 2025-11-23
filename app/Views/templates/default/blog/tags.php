<?= $this->extend('Views/templates/default/base') ?>

<?= $this->section('metatags') ?>
<?= $seo ?? '' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="py-5 bg-light border-bottom mb-4">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="fw-bolder"><?= esc($tagInfo->tag) ?></h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <li class="breadcrumb-item <?= empty($breadcrumb['url']) ? 'active' : '' ?>"
                        <?= empty($breadcrumb['url']) ? 'aria-current="page"' : '' ?>>
                        <?php if (empty($breadcrumb['url'])): ?>
                            <?= esc($breadcrumb['title']) ?>
                        <?php else: ?>
                            <a href="<?= site_url($breadcrumb['url']) ?>">
                                <?= esc($breadcrumb['title']) ?>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Blog Posts -->
            <div class="col-md-8">
                <div class="row gx-5">
                    <?php if (!empty($blogs)): ?>
                        <?php foreach ($blogs as $blog): ?>
                            <div class="col-lg-6 mb-5">
                                <div class="card h-100 shadow border-0">
                                    <img class="card-img-top"
                                        src="<?= (!empty($blog->seo->coverImage))
                                            ? esc($blog->seo->coverImage)
                                            : 'https://dummyimage.com/600x350/ced4da/6c757d' ?>"
                                        alt="<?= esc($blog->title) ?>" />
                                    <div class="card-body p-4">
                                        <?php if (!empty($blog->tags)): ?>
                                            <?php foreach ($blog->tags as $tag): ?>
                                                <div class="badge bg-primary bg-gradient rounded-pill mb-2">
                                                    <?= esc($tag->tag) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <a class="text-decoration-none link-dark stretched-link"
                                            href="<?= site_url('blog/' . $blog->seflink) ?>">
                                            <div class="h5 card-title mb-3"><?= esc($blog->title) ?></div>
                                        </a>
                                        <p class="card-text mb-0"><?= esc($blog->seo->description ?? '') ?></p>
                                    </div>
                                    <div class="card-footer p-4 pt-0 bg-transparent border-top-0">
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img class="rounded-circle me-3"
                                                    src="https://dummyimage.com/40x40/ced4da/6c757d" alt="..." />
                                                <div class="small">
                                                    <div class="fw-bold">
                                                        <?= esc($blog->author->firstname ?? '') . ' ' . esc($blog->author->sirname ?? '') ?>
                                                    </div>
                                                    <div class="text-muted">
                                                        <?= $dateI18n->createFromTimestamp(strtotime($blog->created_at), app_timezone(), 'tr_TR')->toFormattedDateString(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-muted">No blog posts found for this tag.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-end mb-5">
                    <?= $pager ?? '' ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Tags</h5>
                    </div>
                    <div class="card-body">
                        <input type="text" id="sidebarTagSearch" class="form-control mb-3" placeholder="Search tags...">
                        <div class="row" id="sidebarTagList">
                            <?php if (!empty($allTags)): ?>
                                <?php foreach ($allTags as $tag): ?>
                                    <div class="col-12 mb-2">
                                        <a href="<?= site_url('tag/' . $tag->seflink) ?>"
                                            class="btn btn-outline-primary w-100 text-start sidebar-tag-item">
                                            <?= esc($tag->tag) ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No tags available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('sidebarTagSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const items = document.querySelectorAll('#sidebarTagList .sidebar-tag-item');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.closest('.col-12').style.display = text.includes(term) ? 'block' : 'none';
    });
});
</script>

<?= $this->endSection() ?>