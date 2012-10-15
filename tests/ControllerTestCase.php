<?php

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase{

  protected $application;

  public function setUp(){
    $this->bootstrap = array($this, 'appBootstrap');
    parent::setUp();
  }

  public function appBootstrap(){
    $this->application = new Zend_Application(APPLICATION_ENV, array(
          'config'=>array(
            APPLICATION_PATH . '/configs/application.ini',
            APPLICATION_PATH . '/configs/log.ini',‚
            APPLICATION_PATH . '/configs/routes.ini',
            APPLICATION_PATH . '/configs/unplagged-config.ini'
          )
        ));
    $this->application->bootstrap();
  }

  public function tearDown(){
    $this->resetRequest();
    $this->resetResponse();
    parent::tearDown();
  }

}