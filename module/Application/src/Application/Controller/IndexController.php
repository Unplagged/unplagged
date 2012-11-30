<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Controller\BaseController;

class IndexController extends BaseController
{ 
  
  public function indexAction()
    {
        return new ViewModel();
    }
}
