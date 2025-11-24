<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('metatags') ?>
<?= $seo ?? '' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="py-4 mb-4" style="background-color: #f8fafc;">
    <div class="container px-3" style="max-width: 720px;">
        <div class="text-center mb-3">
            <h1 class="h2 fw-bold" style="color: #074C87;">
                <?= isset($category) ? esc($category->title) : 'Latest Notes' ?>
            </h1>
        </div>
        <nav aria-label="breadcrumb" class="small">
            <ol class="breadcrumb justify-content-center mb-0" style="background: transparent; padding: 0;">
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

<section class="py-4">
    <div class="container px-3">
        <div class="row">
            <div class="<?= (!empty($settings->templateInfos->widgets['sidebar'])) ? 'col-lg-8' : 'col-lg-12' ?>">
                <div class="mx-auto" style="max-width: 720px;">
                    <?php if (empty($blogs)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted" style="font-size: 1.1rem;">No notes found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($blogs as $blog): ?>
                            <?php
                            $seo = !empty($blog->seo) ? json_decode($blog->seo, false) : (object) [];
                            if (!is_object($seo)) $seo = (object) [];

                            $excerpt = !empty($seo->description)
                                ? esc($seo->description)
                                : (!empty($blog->content)
                                    ? esc(word_limiter(strip_tags($blog->content), 30))
                                    : '');

                            $tags = !empty($blog->tags) && is_array($blog->tags) ? $blog->tags : [];
                            ?>
                            <article class="py-4" style="border-bottom: 1px solid #e6f0fa;">
                                <h2 class="h4 mb-2">
                                    <a href="<?= site_url('blog/' . esc($blog->seflink)) ?>" 
                                       class="text-decoration-none" 
                                       style="color: #074C87; transition: color 0.2s;">
                                        <?= esc($blog->title) ?>
                                    </a>
                                </h2>

                                <?php if (!empty($tags)): ?>
                                    <div class="mb-3">
                                        <?php foreach ($tags as $tag): ?>
                                            <?php if (isset($tag->tag)): ?>
                                                <span class="badge me-2 mb-1" 
                                                      style="background-color: #e6f0fa; color: #074C87; padding: 0.35em 0.7em; border-radius: 0.375rem; font-weight: 500; font-size: 0.85rem;">
                                                    <?= esc($tag->tag) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($excerpt): ?>
                                    <p class="text-muted mb-3" style="color: #555; line-height: 1.6;"><?= $excerpt ?></p>
                                <?php endif; ?>

                                <a href="<?= site_url('blog/' . esc($blog->seflink)) ?>"
                                   class="text-decoration-none fw-medium"
                                   style="color: #074C87; display: inline-flex; align-items: center;">
                                    Read more
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-1" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 1 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                    </svg>
                                    <span class="visually-hidden">: <?= esc($blog->title) ?></span>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($pager) && $pager): ?>
                        <div class="mt-5">
                            <?= $pager ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($settings->templateInfos->widgets['sidebar'])): ?>
                <div class="col-lg-4 mt-5 mt-lg-0">
                    <div class="sticky-top" style="top: 90px;">
                        <?= view('templates/default/widgets/sidebar') ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>