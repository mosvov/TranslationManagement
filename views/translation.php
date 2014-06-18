<?php
/*
 * if you have http://www.cniska.net/yii-bootstrap (Yii-Bootstrap) extention you can use code:
 * $this->widget('bootstrap.widgets.TbTabs', array(
 *   'placement'   => 'left',
 *   'encodeLabel' => false,
 *   'tabs'        => $categories,
 *));
 */
?>

<div class="tabs-left">
    <ul id="translation_file_list" class="nav nav-tabs">
        <?php foreach ($categories as $key => $value): ?>
            <li class="<?php if (isset($value['active'])) echo 'active' ?>">
                <a href="#<?= $value['id'] ?>" data-toggle="tab"><?= $value['label'] ?> </a>
            </li>
        <?php endforeach ?>
    </ul>
</div>
<div class="tab-content">
    <?php foreach ($categories as $key => $value): ?>
        <?php Yii::app()->controller->renderPartial('TranslationManagement.views.translation_one', ['category' => $value, 'translated_languages' => $this->translated_languages]) ?>
    <?php endforeach ?>
</div>

<?php Yii::app()->controller->renderPartial('TranslationManagement.views.translation_modal', ['translated_languages' => $this->translated_languages]) ?>