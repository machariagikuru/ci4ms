<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('metatags') ?>
<?= $seo ?? '' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="py-4 mb-4">
    <div class="container px-3" style="max-width: 720px;">
        <div class="text-center mb-3">
            <h1 class="h2 fw-bold">
                <?= isset($category) ? esc($category->title) : 'Blog' ?>
            </h1>
        </div>
        <nav aria-label="breadcrumb" class="text-muted small">
            <ol class="breadcrumb justify-content-center mb-0">
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <li class="breadcrumb-item <?= empty($breadcrumb['url']) ? 'active' : '' ?>"
                        <?= empty($breadcrumb['url']) ? 'aria-current="page"' : '' ?>>
                        <?php if (empty($breadcrumb['url'])): ?>
                            <?= esc($breadcrumb['title']) ?>
                        <?php else: ?>
                            <a href="<?= site_url($breadcrumb['url']) ?>" class="text-muted text-decoration-none"><?= esc($breadcrumb['title']) ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
</header>

<section class="py-4">
    <div class="container px-3">
        <div class="row">
            <div class="<?= (!empty($settings->templateInfos->widgets['sidebar'])) ? 'col-lg-8' : 'col-lg-12' ?>">
                <div class="mx-auto" style="max-width: 720px;">
                    <?php if (empty($blogs)): ?>
                        <p class="text-muted text-center py-5">No blog posts found.</p>
                    <?php else: ?>
                        <?php foreach ($blogs as $blog): ?>
                            <?php
                            // Safely decode SEO metadata
                            $seo = !empty($blog->seo) ? json_decode($blog->seo, false) : (object) [];
                            if (!is_object($seo)) $seo = (object) [];

                            // Use SEO description if available, otherwise fall back to truncated content (if exists)
                            $excerpt = !empty($seo->description)
                                ? esc($seo->description)
                                : (!empty($blog->content)
                                    ? esc(word_limiter(strip_tags($blog->content), 30))
                                    : '');

                            // Tags (optional, kept for categorization context)
                            $tags = !empty($blog->tags) && is_array($blog->tags) ? $blog->tags : [];
                            ?>
                            <article class="py-4 border-bottom border-light">
                                <h2 class="h4 mb-2">
                                    <a href="<?= site_url('blog/' . esc($blog->seflink)) ?>" class="text-decoration-none link-dark">
                                        <?= esc($blog->title) ?>
                                    </a>
                                </h2>

                                <?php if (!empty($tags)): ?>
                                    <div class="mb-3">
                                        <?php foreach ($tags as $tag): ?>
                                            <?php if (isset($tag->tag)): ?>
                                                <span class="badge bg-light text-muted fw-normal me-1"><?= esc($tag->tag) ?></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($excerpt): ?>
                                    <p class="text-muted mb-3"><?= $excerpt ?></p>
                                <?php endif; ?>

                                <a href="<?= site_url('blog/' . esc($blog->seflink)) ?>"
                                   class="link-primary text-decoration-none fw-medium">
                                    Read more<span class="visually-hidden">: <?= esc($blog->title) ?></span>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($pager) && $pager): ?>
                        <div class="mt-5 text-center">
                            <?= $pager ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($settings->templateInfos->widgets['sidebar'])): ?>
                <div class="col-lg-4 mt-5 mt-lg-0">
                    <?= view('templates/default/widgets/sidebar') ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>