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
namespace UnpApplication;

use \UnpApplication\Helper\CaseSelection;
use \UnpApplication\Helper\FlashMessages;
use \UnpCommon\Controller\BaseController;
use \UnpCommon\Controller\Plugin\ActivityStream;
use \Zend\Config\Config;
use \Zend\Config\Factory;
use \Zend\EventManager\EventInterface;
use \Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use \Zend\ModuleManager\Feature\BootstrapListenerInterface;
use \Zend\ModuleManager\Feature\ConfigProviderInterface;
use \Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use \Zend\ServiceManager\Exception\ExceptionInterface;
use \Zend\ServiceManager\ServiceManager;

/**
 * This class is the starting point for the Unplagged application and initalizes 
 * all base components.
 */
class Module implements AutoloaderProviderInterface, BootstrapListenerInterface, ConfigProviderInterface, ViewHelperProviderInterface{

  /**
   * Initalizes the application during the bootstrapping process.
   * 
   * @param EventInterface $e
   */
  public function onBootstrap(EventInterface $e){
    try{
      $serviceManager = $e->getApplication()->getServiceManager();

      $zfcServiceEvents = $serviceManager->get('zfcuser_user_service')->getEventManager();
      $activityStream = $serviceManager->get('controllerpluginmanager')->get('activityStream');

      $zfcServiceEvents->attach('register.post',
              function($e) use($activityStream){
                // @codeCoverageIgnoreStart
                $activityStream->publishActivity('{actor.name} registered', $e->getParam('user'), 'You registered', '',
                        $e->getParam('user'));
                //a user registered here, so we can set the state and send an email        
                //$form = $e->getParam('form');  // Form object
                // @codeCoverageIgnoreEnd
              });

      $this->initDoctrine($serviceManager);
    }catch(ExceptionInterface $e){
      echo "Sorry, there seems to be a problem with our database server, which couldn't be resolved.";
    }
  }

  /**
   * Injects Doctrines entitymanager into every created controller.
   * 
   * @param ServiceManager $serviceManager
   */
  private function initDoctrine(ServiceManager $serviceManager){
    $entityManager = $serviceManager->get('doctrine.entitymanager.orm_default');
    $controllerLoader = $serviceManager->get('ControllerLoader');
    $controllerLoader->addInitializer(function ($controller) use ($entityManager){
              if($controller instanceof BaseController){
                $controller->setEntityManager($entityManager);
              }
            });
  }

  /**
   * Provides the configuration for views.
   * 
   * @return array
   */
  public function getViewHelperConfig(){
    return array(
        'factories'=>array(
            //sets the flashMessages service during the creation of views
            'flashMessages'=>function($sm){
              // @codeCoverageIgnoreStart
              $flashmessenger = $sm->getServiceLocator()
                      ->get('ControllerPluginManager')
                      ->get('flashmessenger');

              $message = new FlashMessages();
              $message->setFlashMessenger($flashmessenger);

              return $message;
              // @codeCoverageIgnoreEnd
            },
            'caseSelection'=>function($sm){
              // @codeCoverageIgnoreStart
              $entityManager = $sm->getServiceLocator()->get('doctrine.entitymanager.orm_default');
              $caseSelection = new CaseSelection();
              $caseSelection->setEntityManager($entityManager);
              
              return $caseSelection;
              // @codeCoverageIgnoreEnd
            }
        ),
    );
  }

  /**
   * This method provides all configuration information of this module. It is expected by ZEND2, so it will be called
   * when the configuration is needed.
   * 
   * @return Config
   */
  public function getConfig(){
    $defaultConfig = new Config(include __DIR__ . '/config/module.config.php');
    $navigationConfig = Factory::fromFile(__DIR__ . '/config/navigation.xml', true);

    $defaultConfig->merge($navigationConfig);
    return $defaultConfig;
  }

  /**
   * Provides information about all modules and libraries that need to be loaded for this module.
   * 
   * @return array The autoloader configuration.
   */
  public function getAutoloaderConfig(){
    return array(
        'Zend\Loader\StandardAutoloader'=>array(
            'namespaces'=>array(
                __NAMESPACE__=>__DIR__ . '/src',
            )
        )
    );
  }

  public function getControllerPluginConfig(){
    return array(
        'factories'=>array(
            'activityStream'=>function ($sm){
              // @codeCoverageIgnoreStart
              $serviceLocator = $sm->getServiceLocator();
              $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');
              $controllerPlugin = new ActivityStream();
              $controllerPlugin->setEntityManager($entityManager);
              return $controllerPlugin;
              // @codeCoverageIgnoreEnd
            },
        ),
    );
  }

}
