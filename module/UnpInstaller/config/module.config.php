<?php

return array(
    'doctrine'=>array(
        'driver'=>array(
            'unplagged_orm'=>array(
                'class'=>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache'=>'array',
                'paths'=>array(__DIR__ . '/../src/Application/Model')
            )
        )
    ),
    'router'=>array(
        'routes'=>array(
            'installer'=>array(
                'type'=>'Zend\Mvc\Router\Http\Literal',
                'options'=>array(
                    'route'=>'/installer',
                    'defaults'=>array(
                        'controller'=>'unpinstaller',
                        'action'=>'index',
                    ),
                ),
            ),
        ),
    ),
    'console'=>array(
        'router'=>array(
            'routes'=>array(
                'update-schema'=>array(
                    'options'=>array(
                        'route'=>'update schema',
                        'defaults'=>array(
                            'controller'=>'unpinstaller',
                            'action'=>'updateSchema'
                        )
                    )
                )
            )
        )
    ),
    'service_manager'=>array(
        'factories'=>array(
            'translator'=>'Zend\I18n\Translator\TranslatorServiceFactory',
        )
    ),
    'translator'=>array(
        'locale'=>'en_EN',
        'fallbackLocale'=>'en_US',
        'translation_file_patterns'=>array(
            array(
                'type'=>'gettext',
                'base_dir'=>__DIR__ . '/../languages',
                'pattern'=>'%s.mo',
            )
        )
    ),
    'controllers'=>array(
        'invokables'=>array(
            'unpinstaller'=>'UnpInstaller\Controller\InstallerController'
        )
    ),
    'view_manager'=>array(
        'display_not_found_reason'=>true,
        'display_exceptions'=>true,
        'doctype'=>'HTML5',
        'not_found_template'=>'error/404',
        'exception_template'=>'error/index',
        'template_map'=>array(
            'layout/layout'=>__DIR__ . '/../view/layout/layout.phtml',
            'installer/index'=>__DIR__ . '/../view/unpinstaller/installer/index.phtml',
        ),
        'template_path_stack'=>array(
            'unpinstaller'=>__DIR__ . '/../view',
        )
    )
);
