<!-- Tags Section -->
<section class="py-5" style="background-color: #e6f0fa;">
    <div class="container px-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder" style="color: #074C87;">Popular Tags</h2>
            <p class="lead" style="color: #074C87; opacity: 0.8;">Discover trending topics</p>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php if (!empty($tags) && is_array($tags)): ?>
                <?php foreach ($tags as $tag): ?>
                    <a href="<?= site_url('tag/' . $tag->seflink) ?>" 
                       class="btn btn-sm"
                       style="background-color: #074C87; color: white; border: 1px solid #074C87; border-radius: 0.375rem; font-weight: 500; padding: 0.25rem 0.75rem; text-decoration: none;">
                        <?= esc($tag->tag) ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center" style="color: #074C87; font-style: italic;">
                    No tags available.
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>