<div class="card border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
    <div class="card-body">
        <?php foreach ($replies as $reply): ?>
            <div class="d-flex mt-4">
                <div class="flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 50px; height: 50px; background-color: #074C87; color: white; font-weight: bold; font-size: 1.1rem;">
                        <?= strtoupper(substr(esc($reply->comFullName), 0, 1)) ?>
                    </div>
                </div>
                <div class="ms-3">
                    <div class="fw-bold" style="color: #074C87; font-size: 0.95rem;">
                        <?= esc($reply->comFullName) ?>
                    </div>
                    <div style="color: #495057; line-height: 1.5; font-size: 0.95rem;">
                        <?= esc($reply->comMessage) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <hr style="border-color: #e6f0fa; margin: 1.25rem 0;">

        <div class="w-100 mt-2">
            <button class="btn w-100"
                    id="loadMore<?= esc($replies[0]->parent_id) ?>"
                    onclick="loadMore('<?= esc($replies[0]->blog_id) ?>', '<?= esc($replies[0]->parent_id) ?>')"
                    data-skip="3"
                    data-defskip="3"
                    style="background-color: #074C87; color: white; font-weight: 500; border: none; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
                Load More
            </button>
        </div>
    </div>
</div>