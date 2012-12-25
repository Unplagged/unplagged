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
namespace UnpInstaller\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * Controller to serve the index and other mostly static pages.
 */
class InstallerController extends AbstractActionController{

  private $em;
  
  public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager){
    $this->em = $entityManager;
  }

  public function indexAction(){
    //return new ViewModel();
  }

  public function updateSchemaAction(){
    echo 'Reading Model classes.';
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
    $metadata = $this->em->getMetadataFactory()->getAllMetadata();
    echo 'Updating database schema';
    $schemaTool->updateSchema($metadata);
    echo 'Finished updating database schema.';
  }

}
