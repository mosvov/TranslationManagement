<div id="<?= $category['id'] ?>" class="tab-pane fade <?php if (isset($category['active'])) echo 'active in' ?>">
    <table class="items table table-bordered">
        <thead>
        <tr>
            <?php foreach ($translated_languages as $lang): ?>
                <th><?= $lang ?></th>
            <?php endforeach; ?>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($category['content'] as $row): ?>
            <?= $row ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
