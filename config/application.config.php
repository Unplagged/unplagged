<?php

return array(
    'unp_settings'=>array(
      'notifications_time_interval' => 86400  
    ),
    'modules'=>array(
        'DoctrineModule',
        'DoctrineORMModule',
        'Application',
        'UnpInstaller',
        'ZfcBase',
        'ZfcUser',
    ),
    'module_listener_options'=>array(
        'config_glob_paths'=>array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths'=>array(
            './module',
            './vendor',
        ),
    ),
    'doctrine'=>array(
        'connection'=>array(
            'orm_default'=>array(
                'driverClass'=>'Doctrine\DBAL\Driver\PDOMySql\Driver',
            ),
        ),
    ),
);
