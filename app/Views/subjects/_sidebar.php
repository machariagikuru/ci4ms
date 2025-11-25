<div class="card mb-4 border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #fff;">
    <div class="card-header" style="background-color: #074C87; color: white; font-weight: 600; padding: 0.75rem 1rem; border-radius: 0.5rem 0.5rem 0 0;">
        <h5 class="mb-0" style="font-size: 1.05rem;">Search Subjects</h5>
    </div>
    <div class="card-body p-3">
        <div class="mb-3">
            <input type="text" 
                   id="subjectSearch" 
                   class="form-control" 
                   placeholder="Search subjects..."
                   style="border: 1px solid #cbd5e1; border-radius: 0.375rem; padding: 0.5rem 1rem; font-size: 0.95rem;">
        </div>
        <div class="list-group" id="subjectList" style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($allSubjects as $s): ?>
                <a href="<?= site_url("subject/{$s->id}") ?>" 
                   class="list-group-item list-group-item-action py-2 px-3<?= (isset($subject) && $subject->id == $s->id) ? ' active' : '' ?>"
                   style="border: 1px solid #e6f0fa; border-radius: 0.375rem; margin-bottom: 0.375rem; color: #074C87; font-weight: 500; font-size: 0.95rem;">
                    <?= esc($s->name) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('subjectSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase().trim();
    const items = document.querySelectorAll('#subjectList a');
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(term) ? 'block' : 'none';
    });
});
</script>

<style>
/* Ensure active item stands out */
#subjectList .list-group-item.active {
    background-color: #074C87 !important;
    color: white !important;
    border-color: #074C87 !important;
}
</style>