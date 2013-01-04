<?php

return array(
    'service_manager'=>array(
        'factories'=>array(
            'translator'=>'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
    ),
    'translator'=>array(
        'locale'=>'en_EN',
        'fallbackLocale'=>'en_US',
        'translation_file_patterns'=>array(
            array(
                'type'=>'gettext',
                'base_dir'=>__DIR__ . '/../languages',
                'pattern'=>'%s.mo',
            ),
        ),
    )
);
