<?php

class IndexControllerTest extends ControllerTestCase {

    public function setUp()
    {
        parent::setUp();
    }
 
    public function testHomepage()
    {
      //$this->dispatch('/');
      
    }
    
    public function testCallWithoutActionShouldPullFromIndexAction()
    {
        //$this->dispatch('/index');
        //$this->assertController('index');
        //$this->assertAction('index');
    }
}
