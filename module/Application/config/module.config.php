<?php

return array(
    'doctrine' => array(
        'driver' => array(
            'unplagged_orm' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '../src/Application/Model')
            )
        )
    ),
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'default' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager'=>array(
        'factories'=>array(
            'translator'=>'Zend\I18n\Translator\TranslatorServiceFactory',
            'navigation'=>'Zend\Navigation\Service\DefaultNavigationFactory'
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
            'Application\Controller\Index'=>'Application\Controller\IndexController'
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
            'application/index/index'=>__DIR__ . '/../view/application/index/index.phtml',
            'error/404'=>__DIR__ . '/../view/error/404.phtml',
            'error/index'=>__DIR__ . '/../view/error/index.phtml',
            'layout/header'=>__DIR__ . '/../view/layout/parts/header.phtml',
            'layout/footer'=>__DIR__ . '/../view/layout/parts/footer.phtml',
            'layout/messages'=>__DIR__ . '/../view/layout/parts/messages.phtml',
            'layout/noscript'=>__DIR__ . '/../view/layout/parts/noscript.phtml',
            'layout/sidebar_menu'=>__DIR__ . '/../view/layout/parts/sidebar_menu.phtml',
            'layout/chrome-frame'=>__DIR__ . '/../view/layout/parts/chrome-frame.phtml'
        ),
        'template_path_stack'=>array(
            __DIR__ . '/../view',
        )
    )
);
