<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('metatags') ?>
<?= $seo ?? '' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<header class="py-5 bg-light border-bottom mb-4">
    <div class="container">
        <div class="text-center my-5">
            <h1 class="fw-bolder">
                <?= isset($category) ? esc($category->title) : 'Blog' ?>
            </h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb): ?>
                    <li class="breadcrumb-item <?= empty($breadcrumb['url']) ? 'active' : '' ?>"
                        <?= empty($breadcrumb['url']) ? 'aria-current="page"' : '' ?>>
                        <?php if (empty($breadcrumb['url'])): ?>
                            <?= esc($breadcrumb['title']) ?>
                        <?php else: ?>
                            <a href="<?= site_url($breadcrumb['url']) ?>"><?= esc($breadcrumb['title']) ?></a>
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
            <div class="<?= (!empty($settings->templateInfos->widgets['sidebar'])) ? 'col-md-9' : 'col-md-12' ?>">
                <div class="px-5">
                    <div class="row gx-5">
                        <?php foreach ($blogs as $blog): ?>
                            <?php
                            // Safely decode SEO; fallback to empty object
                            $seo = !empty($blog->seo) ? json_decode($blog->seo, false) : (object) [];
                            if (!is_object($seo)) {
                                $seo = (object) [];
                            }

                            // Safely get tags (ensure it's iterable)
                            $tags = !empty($blog->tags) && is_array($blog->tags) ? $blog->tags : [];
                            ?>
                            <div class="col-lg-6 mb-5">
                                <div class="card h-100 shadow border-0">
                                    <img class="card-img-top"
                                        src="<?= !empty($seo->coverImage) ? esc($seo->coverImage) : 'https://dummyimage.com/600x350/ced4da/6c757d' ?>"
                                        alt="<?= esc($blog->title) ?>" />
                                    <div class="card-body p-4">
                                        <?php foreach ($tags as $tag): ?>
                                            <div class="badge bg-primary bg-gradient rounded-pill mb-2">
                                                <?= isset($tag->tag) ? esc($tag->tag) : '' ?>
                                            </div>
                                        <?php endforeach; ?>

                                        <a class="text-decoration-none link-dark stretched-link"
                                            href="<?= site_url('blog/' . esc($blog->seflink)) ?>">
                                            <div class="h5 card-title mb-3"><?= esc($blog->title) ?></div>
                                        </a>

                                        <p class="card-text mb-0">
                                            <?= isset($seo->description) ? esc($seo->description) : '' ?>
                                        </p>
                                    </div>

                                    <div class="card-footer p-4 pt-0 bg-transparent border-top-0">
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img class="rounded-circle me-3"
                                                    src="https://dummyimage.com/40x40/ced4da/6c757d" alt="Author" />
                                                <div class="small">
                                                    <?php
                                                    $authorName = '';
                                                    if (!empty($blog->author)) {
                                                        $authorName = trim(esc($blog->author->firstname ?? '') . ' ' . esc($blog->author->sirname ?? ''));
                                                    }
                                                    if (empty($authorName)) {
                                                        $authorName = lang('Blog.unknownAuthor') ?? 'Anonymous';
                                                    }
                                                    ?>
                                                    <div class="fw-bold"><?= $authorName ?></div>
                                                    <div class="text-muted">
                                                        <?php if (!empty($blog->created_at) && $blog->created_at !== '0000-00-00 00:00:00'): ?>
                                                            <?php
                                                            try {
                                                                $date = \CodeIgniter\I18n\Time::createFromTimestamp(strtotime($blog->created_at), app_timezone(), 'tr_TR');
                                                                echo $date->toLocalizedString('dd MMMM yyyy HH:mm');
                                                            } catch (Exception $e) {
                                                                echo esc($blog->created_at);
                                                            }
                                                            ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (isset($pager) && $pager): ?>
                        <div class="text-end mb-5 mb-xl-0">
                            <?= $pager ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($settings->templateInfos->widgets['sidebar'])): ?>
                <?= view('templates/default/widgets/sidebar') ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>