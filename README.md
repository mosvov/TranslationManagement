TranslationManagement
=====================

Yii extension for manage translation from CPhpMessageSource

Demo
------------
<img src="http://oi61.tinypic.com/2aga2x.jpg" width="700px">
<img src="http://oi60.tinypic.com/s1166r.jpg" width="700px">

Install
------------

Just copy directory "TranslationManagement" to your /protected/extensions/ folder.

Or `git clone git@github.com:mosvov/TranslationManagement.git`

Usage
------------
#### Simple widget in any view file
```php
$this->widget('application.extensions.TranslationManagement.TranslationManagement',array(
    'default_language'=> 'ru',
    'translated_languages' => ['ru'=>'Russian', 'en'=>'English']
));
```

### Or you can configure message in your config file
```php
    // @usage Yii::app()->params['paramName']
	'params'=>array(
        'translatedLanguages'=>array(
            'ru' => 'Русский',
            'en' => 'English',
            'zh' => '中国的',
            'ar' => 'العربية'
        ),
        'defaultLanguage'=>'ru',
    )
```
then you can include widget like this
```php
$this->widget('application.extensions.TranslationManagement.TranslationManagement');
```

For work it needs completely generated message in message directory.
Full guide to generate message you can found here http://www.yiiframework.com/doc/guide/1.1/fr/topics.i18n