<?php

/**
 * Unplagged - The plagiarism detection cockpit.
 * Copyright (C) 2012 Unplagged
 *  
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * 
 * Contains all global default settings, that the installer may override but
 * not necessarily has to. It also knows the path to the autogenerated config
 * file.
 */
return array(
    'unp_settings'=>array(
        'imprint_enabled'=>false,
        //relative to base directory
        'installer_config_file'=> 'config/autoload/settings.local.php',
        'notifications_time_interval'=>86400,
        'installer_enabled'=>true,
        //directories here need to be relative to the base directory
        'installation_directories'=>array(
            'writeable'=>array(
                'resources',
                'config/autoload',
            ),
            'create'=>array(
                'resources/uploads',
                'resources/uploads/avatars',
                'resources/downloads',
                'resources/downloads/reports',
                'resources/logs',
                'resources/temp',
                'resources/temp/cache',
                'resources/temp/ocr',
                'resources/temp/imagemagick',
            ),
        )
    ),
    'doctrine'=>array(
        'orm_autoload_annotations'=>true,
        'driver'=>array(
            'unp_orm'=>array(
                'class'=>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache'=>'array',
                'paths'=>array(__DIR__ . '/../../module/UnpCommon/src/Model'),
            ),
            'orm_default'=>array(
                'drivers'=>array(
                    'UnpCommon\Model'=>'unp_orm'
                ),
            ),
        ),
        'configuration'=>array(
            'orm_default'=>array(
                'proxy_dir' => 'resources/Doctrine/Proxies',
            ),
        ),
    ),
);