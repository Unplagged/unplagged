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
namespace UnpApplicationTest\Helper;

use \DateTime;
use \PHPUnit_Framework_TestCase;
use \UnpApplication\Helper\CaseSelection;
use \Zend\View\Renderer\PhpRenderer;

/**
 * 
 */
class CaseSelectionTest extends PHPUnit_Framework_TestCase{

  private $object;

  protected function setUp(){
    $translator = \Zend\I18n\Translator\Translator::factory(array('locale'=>'en_EN',
        'fallbackLocale'=>'en_US',
        'translation_file_patterns'=>array(
            array(
                'type'=>'gettext',
                'base_dir'=>__DIR__ . '/../languages',
                'pattern'=>'%s.mo',
            )
        )));
    $view = new PhpRenderer();
    $pluginManager = new \Zend\View\HelperPluginManager();
    $translate = new \Zend\I18n\View\Helper\Translate();
    $translate->setTranslator($translator);
    $pluginManager->setService('translate', $translate);
    $view->setHelperPluginManager($pluginManager);
    
    $repositoryMock = $this->getMock('\Doctrine\ORM\EntityRepository');
    
    $this->object = new DateFormat();
    $this->object->setView($view);
  }

  public function testNow(){
    $result = $this->object->expiredTime(new DateTime('now'));
    
    $this->assertEquals('Now', $result);
  }

}