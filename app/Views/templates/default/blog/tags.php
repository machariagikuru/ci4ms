<?= $this->extend('Views/templates/default/base') ?>

<?= $this->section('metatags') ?>
<?= $seo ?? '' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="py-5 mb-4" style="background-color: #f8fafc; border-bottom: 1px solid #e6f0fa;">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="fw-bolder" style="color: #074C87;"><?= esc($tagInfo->tag) ?></h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center" style="background: transparent; padding: 0;">
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <li class="breadcrumb-item <?= empty($breadcrumb['url']) ? 'active' : '' ?>"
                        <?= empty($breadcrumb['url']) ? 'aria-current="page"' : '' ?>
                        style="<?= empty($breadcrumb['url']) ? 'color: #074C87;' : '' ?>">
                        <?php if (empty($breadcrumb['url'])): ?>
                            <?= esc($breadcrumb['title']) ?>
                        <?php else: ?>
                            <a href="<?= site_url($breadcrumb['url']) ?>" class="text-decoration-none" style="color: #074C87;">
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
                                <div class="card h-100 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
                                    <div class="card-body p-4">
                                        <?php if (!empty($blog->tags)): ?>
                                            <?php foreach ($blog->tags as $tag): ?>
                                                <span class="badge me-2 mb-2" 
                                                      style="background-color: #e6f0fa; color: #074C87; padding: 0.35em 0.7em; border-radius: 0.375rem; font-weight: 500; font-size: 0.85rem;">
                                                    <?= esc($tag->tag) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <h5 class="card-title mb-2">
                                            <a href="<?= site_url('blog/' . $blog->seflink) ?>" 
                                               class="text-decoration-none" 
                                               style="color: #074C87; font-weight: 600;">
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
                                        <p class="card-text text-muted mb-3" style="color: #555; line-height: 1.5;"><?= esc($excerpt) ?></p>
                                        <a href="<?= site_url('blog/' . $blog->seflink) ?>" 
                                           class="btn"
                                           style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.4rem 1rem; font-size: 0.9rem;">
                                            Read more
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-light border" role="alert" style="background-color: #f8fafc; color: #074C87; border-color: #e6f0fa;">
                                No blog posts found for this tag.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-end mb-5">
                    <?= $pager ?? '' ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card mb-4 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
                    <div class="card-header" style="background-color: #074C87; color: white; font-weight: 600; padding: 0.75rem 1rem; border-radius: 0.5rem 0.5rem 0 0;">
                        <h5 class="mb-0" style="font-size: 1.05rem;">Tags</h5>
                    </div>
                    <div class="card-body">
                        <input type="text" 
                               id="sidebarTagSearch" 
                               class="form-control mb-3" 
                               placeholder="Search tags..."
                               style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                        <div class="row" id="sidebarTagList">
                            <?php if (!empty($allTags)): ?>
                                <?php foreach ($allTags as $tag): ?>
                                    <div class="col-12 mb-2">
                                        <a href="<?= site_url('tag/' . esc($tag->seflink)) ?>" 
                                           class="w-100 text-start text-decoration-none sidebar-tag-item"
                                           style="background-color: #e6f0fa; color: #074C87; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.6rem 1rem; display: block;">
                                            <?= esc($tag->tag) ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted" style="font-size: 0.9rem;">No tags available.</p>
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
    const term = e.target.value.toLowerCase().trim();
    const items = document.querySelectorAll('#sidebarTagList .col-12');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? 'block' : 'none';
    });
});
</script>

<?= $this->endSection() ?>