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
 */
namespace UnplaggedTest;

require_once '..' . DIRECTORY_SEPARATOR . 'initApplication.php';

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

error_reporting(E_ALL | E_WARNING);
chdir(__DIR__);

class Bootstrap{

  private static $serviceManager;
  private static $config;

  public function init(){
    $testConfig = include __DIR__ . '/config/test.config.php';
    
    $serviceManager = new ServiceManager(new ServiceManagerConfig());
    $serviceManager->setAllowOverride(true);
    $serviceManager->setService('ApplicationConfig', $testConfig);
    $serviceManager->get('ModuleManager')->loadModules();

    self::$serviceManager = $serviceManager;
    self::$config = $serviceManager->get('Config');
  }

  public static function getServiceManager(){
    return self::$serviceManager;
  }

  public static function getConfig(){
    return self::$config;
  }

}

$bootstrap = new Bootstrap();
$bootstrap->init();