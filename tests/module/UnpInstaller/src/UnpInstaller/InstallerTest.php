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
namespace UnpInstallerTest;

use PHPUnit_Framework_TestCase;
use UnpInstaller\Installer;
use UnplaggedTest\Bootstrap;

/**
 * 
 */
class InstallerTest extends PHPUnit_Framework_TestCase{
  
  private $installer;
  
  public function setUp(){
    $this->installer = new Installer(BASE_PATH, null, null, STDOUT);
  }
  
  public function testIsInstalled(){
    $serviceManager = Bootstrap::getServiceManager();
    $config = $serviceManager->get('Config');
    $this->assertTrue($this->installer->isInstalled($config));
  }
  
  public function testWritePermissions(){
    $directories = array(
        'tests/resources',
        'tests/resources/unreadable-dir'
    );
    $this->assertFalse($this->installer->checkWritePermissions($directories));
  }
  
  public function testIsInstalledWithoutComposer(){
    $serviceManager = Bootstrap::getServiceManager();
    $config = $serviceManager->get('Config');
    $installer = new Installer('', null, null, STDOUT);
    $this->assertFalse($installer->isInstalled($config));
  }
  
  public function testRunComposer(){
    $this->assertTrue($this->installer->runComposer());
  }
  
  public function testRunComposerWithWrongBasePath(){
    $installer = new Installer('', null, null, STDOUT);
    $this->assertFalse($installer->runComposer());
  }
  
  public function testIsInstalledWithMissingDatabaseParameter(){
    $serviceManager = Bootstrap::getServiceManager();
    $config = $serviceManager->get('Config');
    unset($config['doctrine']['connection']['orm_default']['params']['user']);
    $this->assertFalse($this->installer->isInstalled($config));
  }
  
  public function testCheckDatabaseConnection(){
    $databaseConfig = array(
            'driverClass'=>'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'path'     => __DIR__ . '/../../../../resources/basic.db',
                )
           );
    
    $this->assertTrue($this->installer->checkDatabaseConnection($databaseConfig));
  }
  
  public function testCheckDatabaseWithoutDriverClass(){
    $this->assertFalse($this->installer->checkDatabaseConnection(array()));
  }
  
  public function testCheckDatabaseConnectionFailing(){
    $databaseConfig = array(
            'driverClass'=>'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    //not existent on purpose
                    'path'     => __DIR__ . '/resources/basic.db',
                )
           );
    $this->assertFalse($this->installer->checkDatabaseConnection($databaseConfig));
  }
  
  public function testCreateConfigFileWithExistingConfig(){
    $path = BASE_PATH . '/tests/resources/';
    $fileName =  'simple.config.php';
    $fullFilePath = $path . $fileName;
    $installer = new Installer($path, null, null, STDOUT);
    $installer->createConfigFile($fileName, array(), true);
    $this->assertTrue(file_exists($fullFilePath));
  }
  
  public function testDeleteDatabaseSchemaRuns(){
    $entityManager = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
    $this->installer->deleteDatabaseSchema($entityManager);
  }
  
  public function testInstallDirectories(){
    $directories = array(
        'tests/resources/unreadable-dir/test-directory/',
        'tests/resources/tmp/test-directory/'
    );
    
    $this->assertFalse($this->installer->installDirectories($directories));
    $this->assertTrue(is_dir(BASE_PATH . '/tests/resources/tmp/test-directory/'));
    rmdir(BASE_PATH . '/tests/resources/tmp/test-directory/');
  }
  
  /**
   * 
   */
  public function testAdminCreated(){
    $entityManager = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
    $this->assertTrue($this->installer->adminCreated($entityManager));
  }
  
  public function testCreateAdmin(){
    $entityManager = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
    $this->assertTrue($this->installer->createAdmin($entityManager, array()));
  }
}