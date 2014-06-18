<?php

/**
 * Yii extension for manage translation from CPhpMessageSource
 *
 * @author Volodymyr Moskalyk <mosvov@gmail.com>
 * @version 1.0.0
 * @license Apache License Version 2.0, January 2004, http://www.apache.org/licenses/
 */
class TranslationManagement extends CInputWidget {

    /**
     * Default message language
     * @var
     */
    public $default_language;
    /**
     * Language array of translated language
     * @var array
     */
    public $translated_languages = array();

    /**
     * Here we start
     */
    public function run() {
        Yii::setPathOfAlias('TranslationManagement', dirname(__FILE__));

        $this->registerAssets();

        if (empty($this->default_language))
            $this->default_language = Yii::app()->params['defaultLanguage'];

        if (!$this->translated_languages)
            $this->translated_languages = Yii::app()->params['translatedLanguages'];

        //if (Yii::app()->user->isGuest) throw new CHttpException(404);

        //if (!Yii::app()->user->checkAccess('superadmin') and !Yii::app()->user->checkAccess('translator'))
        //throw new CHttpException(404);


        if (Yii::app()->getRequest()->isAjaxRequest) {
            foreach ($_POST as $key => $value) {
                if (in_array($key, ['key_name', 'file_name'])) continue;
                $tmp = explode('_', $key);
                $this->translateOneMessage(urldecode($_POST['key_name']), $value, $tmp[1], $_POST['file_name']);
            }

            ob_clean();
            $data = $this->getMessage(Yii::app()->basePath.'/messages/'.$this->default_language.'/', $_POST['file_name']);
            echo Yii::app()->controller->renderPartial('TranslationManagement.views.translation_one', [
                'category'             => ['id' => str_replace('.php', '', $_POST['file_name']), 'content' => $data['rows'], 'active' => true],
                'translated_languages' => $this->translated_languages
            ]);
            Yii::app()->end();
        }

        $this->render('translation', ['categories' => $this->getCategories(), 'translated_languages' => $this->translated_languages]);
    }

    /**
     * Register bootstrap and js, css
     */
    public function registerAssets() {
        Yii::app()->getClientScript()->scriptMap['bootstrap.js'] = false; // if bootstrap.js already register remove it. We use bootstrap.min.js

        Yii::app()->getClientScript()
            ->addPackage('TranslationManagement', array(
                'baseUrl' => CHtml::asset(Yii::getPathOfAlias('TranslationManagement.assets')),
                'js'      => ['js/bootstrap.min.js', 'js/translation.js'],
                'css'     => ['css/bootstrap.min.css', 'css/translation.css'],
                'depends' => ['jquery'],
            ))
            ->registerPackage('TranslationManagement');
    }

    /**
     * Get array of all message for all category
     *
     * @param bool $directory
     * @return array
     */
    public function getCategories($directory = false) {
        $categories = [];

        $dir = $directory ? $directory : Yii::app()->basePath.'/messages/'.$this->default_language.'/';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file == '.' || $file == '..') continue;
                    $data              = $this->getMessage($dir, $file);
                    $name              = str_replace('.php', '', $file);
                    $categories[$name] = array(
                        'label'   => $name.' <span class="badge badge-'.$data['total_class'].'">'.$data['total_count'].'%</span>',
                        'id'      => $name,
                        'content' => $data['rows'],
                    );
                }
                closedir($dh);
            }
        }
        ksort($categories);
        $categories[key($categories)]['active'] = true; // make first element active

        return $categories;
    }


    /**
     * Generate table with message for one category
     *
     * @param $directory
     * @param $file
     * @return array
     * @throws Exception
     */
    public function getMessage($directory, $file) {
        $message   = $row = [];
        $count_bad = 0;

        foreach ($this->translated_languages as $key => $lang) {
            $path = str_replace('/'.$this->default_language.'/', '/'.$key.'/', $directory);
            if (file_exists($path.$file)) {
                $message[$key] = require($path.$file);
            } else {
                throw new Exception(
                    sprintf('File "%s" with translation does not exist. Please run "/protected$ ./yiic message messages/config.php" to synchronise you translation files', $path.$file)
                );
            }
        }

        $count_all = count($message[$this->default_language]) * count(array_keys($this->translated_languages));


        foreach ($message[$this->default_language] as $key_t => $value_t) {
            $row[$key_t] = '<tr>';

            $default_message = ($value_t) ? $value_t : $key_t;
            $row[$key_t] .= '<td>'.$default_message.'</td>';
            foreach (array_keys($this->translated_languages) as $language) {
                if ($language == $this->default_language) continue;
                if (isset($message[$language][$key_t]) and $message[$language][$key_t]) {
                    $row[$key_t] .= '<td>'.$message[$language][$key_t].'</td>';
                } else {
                    $row[$key_t] .= '<td class="error"></td>';
                    $count_bad++;
                }
            }

            $row[$key_t] .= '<td>'.
                CHtml::link('<i class="icon-pencil"></i>', '#', [
                    'class'       => 'update',
                    'data-file'   => $file,
                    'data-key'    => urlencode($key_t),
                    'data-toggle' => 'modal',
                    'data-target' => '#translate_modal'
                ]).'</td>';

            $row[$key_t] .= '</tr>';
        }

        $result = 100 - ($count_bad * 100 / $count_all);
        if ($result == 100) {
            $class = 'success';
        } elseif ($result >= 50 and $result <= 99.99) {
            $class = 'warning';
        } else {
            $class = 'important';
        }

        return array('rows' => $row, 'total_class' => $class, 'total_count' => number_format($result, 2));
    }

    /**
     * Save translated message to file
     *
     * @see \MessageCommand::generateMessageFile
     * @param $key
     * @param $message
     * @param $lang
     * @param $file
     * @throws Exception
     */
    public static function translateOneMessage($key, $message, $lang, $file) {
        $file       = Yii::app()->basePath.'/messages/'.$lang.'/'.$file;
        $translated = require($file);

        $translated[$key] = $message;

        //ksort($translated);

        $array   = str_replace("\r", '', var_export($translated, true));
        $content = <<<EOD
<?php
/**
 * Message translations.
 *
 * This file is automatically generated by 'yiic message' command.
 * It contains the localizable messages extracted from source code.
 * You may modify this file by translating the extracted messages.
 *
 * Each array element represents the translation (value) of a message (key).
 * If the value is empty, the message is considered as not translated.
 * Messages that no longer need translation will have their translations
 * enclosed between a pair of '@@' marks.
 *
 * Message string can be used with plural forms format. Check i18n section
 * of the guide for details.
 *
 * NOTE, this file must be saved in UTF-8 encoding.
 */
return $array;

EOD;
        if (is_writable($file)) {
            file_put_contents($file, $content);
        } else {
            throw new Exception(sprintf('File "%s" is not writable', $file));
        }
    }
}