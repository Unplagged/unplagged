<?php

/**
 * This file contains all config values set during the installation process or 
 * later on by the administrator of the application.
 * 
 *  @todo store the config values in the db and autogenerate this file from there everytime an update is performed 
 */
return array(
    'unp_settings'=>array(
        'installer_enabled'=>true,
    ),
    'contact'=>array(
        'address'=>array(
            'street'=>'Street 123',
            'zip'=>'12345',
            'city'=>'Berlin',
            'telephone'=>'+49 30 123 45 67'
        ),
        'email'=>'info@example.com',
        'name'=>'John Doe',
    ),
    'doctrine'=>array(
        'connection'=>array(
            'orm_default'=>array(
                'driverClass'=>'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params'=>array(
                    'host'=>'localhost',
                    'port'=>'3306',
                    'user'=>'unplagged',
                    'password'=>'Q,|R[+(2BLJ\\',
                    'dbname'=>'unplagged'
                ),
            )
        )
    )
);