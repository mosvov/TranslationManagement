<div id="translate_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form id="translate_form">
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
            <h4>Translate</h4>
        </div>
        <div class="modal-body">

            <ul class="nav nav-tabs">
                <?php
                $count = 0;
                foreach ($translated_languages as $key => $language): ?>
                    <li class="<?php if ($count++ === 0) echo 'active' ?>">
                        <a href="#<?= $key ?>" data-toggle="tab"><?= $language ?> </a>
                    </li>
                <?php endforeach ?>
            </ul>
            <div class="tab-content">
                <?php
                $count = 0;
                foreach ($translated_languages as $key => $language): ?>
                    <div id="<?= $key ?>" class="tab-pane fade <?php if ($count++ === 0) echo 'active in' ?>">
                        <textarea id="translate_<?= $key ?>" name="translate_<?= $key ?>"></textarea>
                    </div>
                <?php endforeach ?>
            </div>
            <input type="hidden" name="file_name" id="file_name">
            <input type="hidden" name="key_name" id="key_name">

        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <?= CHtml::ajaxSubmitButton('Save', '', [
                'success' => 'js: function(html){
                    var file_id =  $("#file_name").val().replace(".php", "");
                    $("#"+file_id).replaceWith(html);
                    $("#translate_modal a.close").click();
                }',
                'error'   => 'js: function(data){alert(data.responseText)}'
            ], [
                'class' => 'btn btn-primary'
            ]);
            ?>
        </div>
    </form>
</div>
