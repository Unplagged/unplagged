<?php

/**
 * This file is an example configuration file for the Unplagged application
 * and is used by the Installer as the default configuration.
 *  
 * If you don't want to use the provided Installer, you can simply copy this
 * file to your config/autoload/ directory and fill in all necessary values 
 * by hand.
 */
return array(
    'unp_settings'=>array(
        'default_language'=>'en_EN',
        'development_mode'=>'false',
        'tesseract'=>array(
            'tesseract_call'=>'tesseract',
            'available_languages'=>array('en'),
        ),
        'ghostscript'=>array(
            'ghostscript_call'=>'gs'
        ),
        'imagemagick'=>array(
            'imagemagick_call'=>'convert'
        ),
        'paginator'=>array(
            'items_per_page'=>15
        ),
        'notifications'=>array(
            'notifications_time_interval'=>86400
        ),
        'imprint_enabled'=>false,
        'mailer'=>array(
            'sender_name'=>'',
            'sender_mail'=>'',
        ),
    ),
    'contact'=>array(
        'address'=>array(
            'street'=>'',
            'zip'=>'',
            'city'=>'',
            'telephone'=>'',
        ),
        'email'=>'',
        'name'=>'',
    ),
    'doctrine'=>array(
        'connection'=>array(
            'orm_default'=>array(
                'params'=>array(
                    'host'=>'localhost',
                    'port'=>'3306',
                    'user'=>'unplagged',
                    'password'=>'',
                    'dbname'=>'unplagged',
                ),
            ),
        ),
    ),
);