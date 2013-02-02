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
use \UnpApplication\Helper\DateFormat;
use \Zend\View\Renderer\PhpRenderer;

/**
 * 
 */
class DateFormatTest extends PHPUnit_Framework_TestCase{

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
    $translatePlural = new \Zend\I18n\View\Helper\TranslatePlural();
    $translatePlural->setTranslator($translator);
    $pluginManager->setService('translate', $translate);
    $pluginManager->setService('translatePlural', $translatePlural);
    $view->setHelperPluginManager($pluginManager);
    
    $this->object = new DateFormat();
    $this->object->setView($view);
  }

  /**
   * If undeterminism of dates makes the result unpredictable, you need to
   * set the second parameter to the same date as the first.
   */
  public function testNow(){
    $result = $this->object->expiredTime(new DateTime('now'));
    
    $this->assertEquals('Now', $result);
  }
  
  public function testAFewSecondsAgo(){
    $now = new \DateTime('now');
    $before = new \DateTime('now');
    $before->setTimestamp($now->getTimestamp()-30);//subtract 30 seconds
    
    $this->assertEquals('a few seconds ago', $this->object->expiredTime($before, $now));
  }

    
  public function testAMinutesAgo(){
    $now = new \DateTime('now');
    $before = new \DateTime('now');
    $before->setTimestamp($now->getTimestamp()-80);//subtract about 1 minute
    
    $this->assertEquals('a minute ago', $this->object->expiredTime($before, $now));
  }
  
  public function testSomeHoursAgo(){
    $now = new \DateTime('now');
    $before = new \DateTime('now');
    $before->setTimestamp($now->getTimestamp()- (60 * 60 * 8.5));//subtract about 8 hours
    
    $this->assertEquals('8 hours ago', $this->object->expiredTime($before, $now));
  }
  
  public function testSomeDaysAgo(){
    $now = new \DateTime('now');
    $before = new \DateTime('now');
    $before->setTimestamp($now->getTimestamp()- (60 * 60 * 24 * 5));//subtract about 5 days
    
    $this->assertRegExp('/^5 days ago/', $this->object->expiredTime($before, $now));
  }
  
  public function testSomeWeeksAgo(){
    $now = new \DateTime('now');
    $before = new \DateTime('now');
    $before->setTimestamp($now->getTimestamp()- (60 * 60 * 24 * 7 * 3));//subtract about 3 weeks
    
    $this->assertEquals('3 weeks ago', $this->object->expiredTime($before, $now));
  }
    
  public function testSomeMonthsAgo(){
    $now = new \DateTime('now');
    $before = new \DateTime('now');
    $before->setTimestamp($now->getTimestamp()- (60 * 60 * 24 * 30 * 4.35));//subtract about 4 months
    
    $this->assertEquals('4 months ago', $this->object->expiredTime($before, $now));
  }
  
  public function testSomeYearsAgo(){
    $now = new \DateTime('now');
    $before = new \DateTime('now');
    $before->setTimestamp($now->getTimestamp()- (60 * 60 * 24 * 365 * 5.5));//subtract about 5 years
    
    $this->assertEquals('5 years ago', $this->object->expiredTime($before, $now));
  }

}