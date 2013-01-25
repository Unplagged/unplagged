<?php
return array(
    'router'=>array(
        'routes'=>array(
            'home'=>array(
                'type'=>'Zend\Mvc\Router\Http\Segment',
                'options'=>array(
                    'route'=>'/[page/:page]',
                    'constraints'=>array(
                        'id'=>'[0-9]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'UnpApplication\Controller\Activity',
                        'action'=>'recent-activity',
                        'page'=>'0'
                    ),
                ),
            ),
            'about'=>array(
                'type'=>'Zend\Mvc\Router\Http\Literal',
                'options'=>array(
                    'route'=>'/about',
                    'defaults'=>array(
                        'controller'=>'UnpApplication\Controller\Index',
                        'action'=>'about',
                    ),
                ),
            ),
            'imprint'=>array(
                'type'=>'Zend\Mvc\Router\Http\Literal',
                'options'=>array(
                    'route'=>'/imprint',
                    'defaults'=>array(
                        'controller'=>'UnpApplication\Controller\Index',
                        'action'=>'imprint',
                    ),
                ),
            ),
            'images'=>array(
                'type'=>'Zend\Mvc\Router\Http\Segment',
                'options'=>array(
                    'route'=>'/uploads[/:id]',
                    'constraints'=>array(
                        'id'=>'[0-9]*'
                    ),
                    'defaults'=>array(
                        'controller'=>'UnpApplication\Controller\File',
                        'action'=>'view',
                        'id'=>''
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'default'=>array(
                'type'=>'Literal',
                'options'=>array(
                    'route'=>'/application',
                    'defaults'=>array(
                        '__NAMESPACE__'=>'UnpApplication\Controller',
                        'controller'=>'Index',
                        'action'=>'index',
                    ),
                ),
                'may_terminate'=>true,
                'child_routes'=>array(
                    'default'=>array(
                        'type'=>'Segment',
                        'options'=>array(
                            'route'=>'/[:controller[/:action]]',
                            'constraints'=>array(
                                'controller'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'=>'[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults'=>array(
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
            'navigation'=>'Zend\Navigation\Service\DefaultNavigationFactory',
            'main_navigation'=>'UnpApplication\Factory\MainNavigationFactory',
            'footer_navigation'=>'UnpApplication\Factory\FooterNavigationFactory',
        )
    ),
    'view_helpers'=>array(
        'invokables'=>array(
            'dateFormat'=>'UnpApplication\Helper\DateFormat',
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
            'UnpApplication\Controller\Index'=>'UnpApplication\Controller\IndexController',
            'UnpApplication\Controller\File'=>'UnpApplication\Controller\FileController',
            'UnpApplication\Controller\Activity'=>'UnpApplication\Controller\ActivityStreamController',
        )
    ),
    'view_manager'=>array(
        'display_not_found_reason'=>true,
        'display_exceptions'=>true,
        'not_found_template'=>'error/404',
        'exception_template'=>'error/index',
        'template_map'=>array(
            'layout/layout'=>__DIR__ . '/../view/layout/layout.phtml',
            'layout/header'=>__DIR__ . '/../view/layout/parts/header.phtml',
            'layout/footer'=>__DIR__ . '/../view/layout/parts/footer.phtml',
            'layout/messages'=>__DIR__ . '/../view/layout/parts/messages.phtml',
            'layout/noscript'=>__DIR__ . '/../view/layout/parts/noscript.phtml',
            'layout/login'=>__DIR__ . '/../view/layout/parts/login.phtml',
            'layout/sidebar_menu'=>__DIR__ . '/../view/layout/parts/sidebar_menu.phtml',
            'layout/chrome-frame'=>__DIR__ . '/../view/layout/parts/chrome-frame.phtml',
            'partials/comments'=>__DIR__ . '/../view/partial/comments.phtml',
            'unp-application/index/imprint'=>__DIR__ . '/../view/application/index/imprint.phtml',
            'unp-application/activity-stream/recent-activity'=>__DIR__ . '/../view/application/activity-stream/recent-activity.phtml',
            'unp-application/index/about'=>__DIR__ . '/../view/application/index/about.phtml',
            'unp-application/index/index'=>__DIR__ . '/../view/application/index/index.phtml',
            'error/404'=>__DIR__ . '/../view/error/404.phtml',
            'error/index'=>__DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack'=>array(
            __DIR__ . '/../view',
        )
    )
);
