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
namespace ApplicationTest; //Change this namespace for your test

require_once '..' . DIRECTORY_SEPARATOR . 'initApplication.php';

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_WARNING);
chdir(__DIR__);

class Bootstrap{

  protected static $serviceManager;
  protected static $config;
  protected static $bootstrap;

  public static function init(){
    // Load the user-defined test configuration file, if it exists; otherwise, load
    if(is_readable(__DIR__ . '/TestConfig.php')){
      $testConfig = include __DIR__ . '/TestConfig.php';
    }else{
      $testConfig = include __DIR__ . '/TestConfig.php.dist';
    }

    $zf2ModulePaths = array();

    if(isset($testConfig['module_listener_options']['module_paths'])){
      $modulePaths = $testConfig['module_listener_options']['module_paths'];
      foreach($modulePaths as $modulePath){
        if(($path = static::findParentPath($modulePath))){
          $zf2ModulePaths[] = $path;
        }
      }
    }

    $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
    $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ? : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

    static::initAutoloader();

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

    static::$serviceManager = $serviceManager;
    static::$config = $config;
  }

  public static function getServiceManager(){
    return static::$serviceManager;
  }

  public static function getConfig(){
    return static::$config;
  }

  protected static function initAutoloader(){
    $vendorPath = static::findParentPath('vendor');

    if(is_readable($vendorPath . '/autoload.php')){
      $loader = include $vendorPath . '/autoload.php';
    }else{
      $zf2Path = getenv('ZF2_PATH') ? : (defined('ZF2_PATH') ? ZF2_PATH : (is_dir($vendorPath . '/ZF2/library') ? $vendorPath . '/ZF2/library' : false));

      if(!$zf2Path){
        throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
      }

      include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
    }

    AutoloaderFactory::factory(array(
        'Zend\Loader\StandardAutoloader'=>array(
            'autoregister_zf'=>true,
            'namespaces'=>array(
                __NAMESPACE__=>__DIR__ . '/' . __NAMESPACE__,
            ),
        ),
    ));
  }

  protected static function findParentPath($path){
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

Bootstrap::init();