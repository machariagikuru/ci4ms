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
                                    <div class="card-body p-4">
                                        <?php if (!empty($blog->tags)): ?>
                                            <?php foreach ($blog->tags as $tag): ?>
                                                <div class="badge bg-primary bg-gradient rounded-pill mb-2">
                                                    <?= esc($tag->tag) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <h5 class="card-title mb-2">
                                            <a href="<?= site_url('blog/' . $blog->seflink) ?>" class="text-decoration-none text-dark">
                                                <?= esc($blog->title) ?>
                                            </a>
                                        </h5>
                                        <?php
                                        $content = $blog->content ?? '';
                                        $plainText = trim(strip_tags($content));
                                        if ($plainText === '') {
                                            $excerpt = 'No excerpt available.';
                                        } else {
                                            $maxLength = 120;
                                            if (strlen($plainText) > $maxLength) {
                                                $excerpt = substr($plainText, 0, $maxLength - 3) . '...';
                                            } else {
                                                $excerpt = $plainText;
                                            }
                                        }
                                        ?>
                                        <p class="card-text text-muted mb-3"><?= esc($excerpt) ?></p>
                                        <a href="<?= site_url('blog/' . $blog->seflink) ?>" class="btn btn-outline-primary btn-sm px-3">
                                            Read more
                                        </a>
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