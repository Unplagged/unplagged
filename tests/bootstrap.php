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

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;

error_reporting(E_ALL | E_WARNING);
chdir(__DIR__);

class Bootstrap{

  private static $serviceManager;
  private static $config;

  public function init(){
    $testConfig = include __DIR__ . '/TestConfig.php.dist';

    $zf2ModulePaths = array();
    $modulePaths = $testConfig['module_listener_options']['module_paths'];
    foreach($modulePaths as $modulePath){
      if(($path = $this->findParentPath($modulePath))){
        $zf2ModulePaths[] = $path;
      }
    }

    $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;

    $this->initAutoloader();

    // use ModuleManager to load this module and it's dependencies
    $baseConfig = array(
        'module_listener_options'=>array(
            'module_paths'=>explode(PATH_SEPARATOR, $zf2ModulePaths),
        ),
    );

    $config = ArrayUtils::merge($baseConfig, $testConfig);

    $serviceManager = new ServiceManager(new ServiceManagerConfig());
    $serviceManager->setService('ApplicationConfig', $config);
    $serviceManager->get('ModuleManager')->loadModules();

    self::$serviceManager = $serviceManager;
    self::$config = $config;
  }

  public static function getServiceManager(){
    return self::$serviceManager;
  }

  public static function getConfig(){
    return self::$config;
  }

  private function initAutoloader(){
    $vendorPath = $this->findParentPath('vendor');
    include $vendorPath . '/autoload.php';

    AutoloaderFactory::factory(array(
        'Zend\Loader\StandardAutoloader'=>array(
            'autoregister_zf'=>true,
            'namespaces'=>array(
                __NAMESPACE__=>__DIR__ . '/' . __NAMESPACE__,
            ),
        ),
    ));
  }

  /**
   * Traverse the directories upwards and see if the given path exists there.
   * 
   * @param string $path
   * @return boolean
   */
  private function findParentPath($path){
    $dir = __DIR__;
    $previousDir = '.';
    while(!is_dir($dir . '/' . $path)){
      $dir = dirname($dir);
      if($previousDir === $dir)
        return false;
      $previousDir = $dir;
    }
    return $dir . '/' . $path;
  }

}

$bootstrap = new Bootstrap();
$bootstrap->init();