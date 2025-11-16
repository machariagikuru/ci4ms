<!-- Tags Section -->
<section class="py-5 bg-light">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder">Popular Tags</h2>
            <p class="lead text-muted">Discover trending topics</p>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php if (!empty($tags) && is_array($tags)): ?>
                <?php foreach ($tags as $tag): ?>
                    <a href="<?= site_url('tag/' . $tag->seflink) ?>" class="btn btn-outline-secondary btn-sm">
                        <?= esc($tag->tag) ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-muted">No tags available.</div>
            <?php endif; ?>
        </div>
    </div>
</section>