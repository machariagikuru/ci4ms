<?= $this->extend('Views/templates/default/base') ?>
<?= $this->section('metatags') ?>
<?= $seo ?? '' ?>
<?= $this->endSection() ?>

<?= $this->section('head') ?>
<?= link_tag('templates/' . ($settings->templateInfos->path ?? 'default') . '/assets/node_modules/sweetalert2/dist/sweetalert2.min.css') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="py-5" style="background-color: #ffffff;">
    <div class="container px-5 my-5">
        <div class="row gx-5">
            <!-- Main Content -->
            <div class="col-md-8">
                <article>
                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb" style="background: transparent; padding: 0;">
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

                    <!-- Title & Meta -->
                    <header class="mb-4 mt-2">
                        <h1 class="fw-bolder mb-2" style="color: #074C87; font-size: 2.25rem; line-height: 1.3;">
                            <?= esc($infos->title ?? '') ?>
                        </h1>

                        <?php if (!empty($infos->created_at) && $infos->created_at !== '0000-00-00 00:00:00'): ?>
                            <div class="text-muted fst-italic mb-3" style="color: #074C87; font-size: 0.95rem;">
                                <?= $dateI18n->createFromTimestamp(strtotime($infos->created_at), app_timezone(), 'tr_TR')->toFormattedDateString(); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Tags -->
                        <?php if (!empty($tags) && is_array($tags)): ?>
                            <div class="mb-4">
                                <?php foreach ($tags as $tag): ?>
                                    <a class="badge text-decoration-none me-2 mb-2"
                                        href="<?= route_to('tag', $tag->seflink ?? '') ?>"
                                        style="background-color: #e6f0fa; color: #074C87; padding: 0.45em 0.8em; border-radius: 0.375rem; font-weight: 500; font-size: 0.9rem;">
                                        <?= esc($tag->tag ?? '') ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </header>

                    <!-- Share Buttons -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <span class="me-3" style="color: #074C87; font-weight: 500;">Share:</span>
                            <a href="https://wa.me/?text=<?= urlencode(site_url('blog/' . ($infos->seflink ?? ''))) . ' - ' . urlencode($infos->title ?? '') ?>"
                                target="_blank" class="btn btn-sm me-2" style="background-color: #25D366; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($infos->title ?? '') ?>&url=<?= urlencode(site_url('blog/' . ($infos->seflink ?? ''))) ?>"
                                target="_blank" class="btn btn-sm me-2" style="background-color: #1DA1F2; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(site_url('blog/' . ($infos->seflink ?? ''))) ?>"
                                target="_blank" class="btn btn-sm me-2" style="background-color: #1877F2; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <button class="btn btn-sm" onclick="copyLink()" 
                                style="background-color: #074C87; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-link-45deg"></i>
                            </button>
                        </div>
                    </div>

                    <hr style="border-color: #e6f0fa;">

                    <!-- Post Content -->
                    <section class="mb-5" style="color: #074C87; line-height: 1.8; font-size: 1.05rem;">
                        <?= $infos->content ?? '' ?>
                    </section>

                    <hr style="border-color: #e6f0fa;">
                </article>

                <!-- Comments -->
                <section>
                    <div class="card" style="background-color: #f9fbfd; border: 1px solid #e6f0fa; border-radius: 0.5rem;">
                        <div class="card-body">
                            <h5 class="mb-4" style="color: #074C87;">Leave a Comment</h5>
                            <form class="mb-4 row">
                                <div class="col-md-6 form-group mb-3">
                                    <input type="text" class="form-control" name="comFullName" placeholder="Full name"
                                        value="<?= old('comFullName') ?>"
                                        style="border-color: #cbd5e1;">
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <input type="email" class="form-control" name="comEmail" placeholder="E-mail"
                                        value="<?= old('comEmail') ?>"
                                        style="border-color: #cbd5e1;">
                                </div>
                                <div class="col-12 form-group mb-3">
                                    <textarea class="form-control" rows="3" name="comMessage"
                                        placeholder="Join the discussion and leave a comment!"
                                        style="border-color: #cbd5e1;"><?= old('comMessage') ?></textarea>
                                </div>
                                <div class="col-6 form-group">
                                    <div class="input-group">
                                        <img src="" class="captcha" alt="captcha">
                                        <input type="text" placeholder="captcha" name="captcha" class="form-control" style="border-color: #cbd5e1;">
                                        <button class="btn" type="button" onclick="captchaF()"
                                            style="background-color: #074C87; color: white; border: none;">
                                            New Captcha
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6 form-group text-end">
                                    <button class="btn btn-sm sendComment" type="button"
                                        data-id=""
                                        data-blogid="<?= esc($infos->id ?? '') ?>"
                                        style="background-color: #074C87; color: white; border: none; padding: 0.4rem 1rem; border-radius: 0.375rem;">
                                        Send
                                    </button>
                                </div>
                            </form>

                            <?php if (!empty($comments)): ?>
                                <hr style="border-color: #e0e9f5;">
                                <div id="comments">
                                    <?= comments($comments, $infos->id ?? ''); ?>
                                </div>
                                <div class="d-flex mt-3">
                                    <div class="w-100">
                                        <button class="btn w-100" onclick="loadMore('<?= esc($infos->id ?? '') ?>')" id="loadMore"
                                            data-skip="5" data-defskip="5"
                                            style="background-color: #074C87; color: white; border: none; border-radius: 0.375rem; padding: 0.5rem;">
                                            <i class=""></i> Load More
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Sidebar: Similar Posts -->
            <div class="col-md-4">
                <div class="sticky-top" style="top: 90px;">
                    <div class="card border-0 shadow-sm" style="border-radius: 0.5rem;">
                        <div class="card-header" style="background-color: #074C87; color: white; font-weight: 600; border-radius: 0.5rem 0.5rem 0 0;">
                            Similar Notes
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($similarPosts) && is_array($similarPosts)): ?>
                                    <?php foreach (array_slice($similarPosts, 0, 5) as $post): ?>
                                        <li class="list-group-item border-0 py-3">
                                            <a href="<?= site_url('blog/' . $post->seflink) ?>" class="text-decoration-none" style="color: #074C87; font-weight: 500;">
                                                <?= esc($post->title) ?>
                                            </a>
                                            <div class="small text-muted mt-1" style="font-size: 0.85rem;">
                                                <?= $dateI18n->createFromTimestamp(strtotime($post->created_at), app_timezone(), 'tr_TR')->toFormattedDateString(); ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item border-0 py-3 text-muted">
                                        No similar notes found.
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<?= script_tag('templates/' . ($settings->templateInfos->path ?? 'default') . '/assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js') ?>
<script>
function copyLink() {
    const url = '<?= site_url('blog/' . ($infos->seflink ?? '')) ?>';
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Link copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });
    });
}
</script>
<?= $this->endSection() ?>