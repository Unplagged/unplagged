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

use UnpCommon\Controller\BaseController;
use UnpInstaller\Installer;
use Zend\Console\Prompt\Confirm;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Select;

/**
 * Provides a console interface mostly for helping during the development of Unplagged.
 */
class ConsoleInstallerController extends BaseController{

  private $outputStream = null;
  
  public function __construct(){
    $this->outputStream = STDOUT;
  }
  
  private function createInstaller(){
    $installer = new Installer(BASE_PATH, $this->getServiceLocator()->get('translator'), null, $this->outputStream);

    return $installer;
  }

  public function setOutputStream($stream){
      $this->outputStream = $stream;
  }
  
  /**
   * Command line action, that deletes all tables from the database.
   */
  public function deleteDatabaseSchemaAction(){
    if(Confirm::prompt('This will delete all saved data! Are you sure you want to continue? [y/n]', 'y', 'n')){
      $installer = $this->createInstaller();
      $installer->deleteDatabaseSchema($this->em);
    }
  }

  /**
   * Questions the user for all necessary information about the database connection and notifies if they would work.
   * 
   * @todo add other db types
   */
  public function checkDatabaseConnectionAction(){
    $options = array(
        '1'=>'MySQL',
        //'2'=>'Sqlite',
    );
    $answer = Select::prompt('Please select your database type.', $options, false, false);
    $config = null;
    switch($answer){
      case '1':
        $config = $this->questionMySqlParameters();
        break;
    }
    $installer = $this->createInstaller();
    $installer->checkDatabaseConnection($config);
  }

  /**
   * Questions the user for all necessary parameters for the MySQL connection.
   * 
   * @return array
   */
  private function questionMySqlParameters(){
    $config = array();
    $config['driverClass'] = 'Doctrine\DBAL\Driver\PDOMySql\Driver';

    $host = Line::prompt(
                    'What is the hostname?(defaults to: localhost)', true, 100
    );
    $port = Line::prompt(
                    'What is the port?(defaults to: 3306)', true, 100
    );
    $user = Line::prompt(
                    'What is the username?(defaults to: unplagged)', true, 100
    );
    $password = Line::prompt(
                    'What is the password?', true, 100
    );
    $dbname = Line::prompt(
                    'What is the name of the database?(defaults to: unplagged)', true, 100
    );

    $config['params'] = array(
        'host'=>!empty($host) ? $host : 'localhost',
        'port'=>!empty($port) ? $port : '3306',
        'user'=>!empty($user) ? $user : 'unplagged',
        'password'=>$password,
        'dbname'=>!empty($dbname) ? $dbname : 'unplagged'
    );
    return $config;
  }

  /**
   * Command line action that updates the database schema from the model files.
   */
  public function updateDatabaseSchemaAction(){
    $installer = $this->createInstaller();
    $installer->updateDatabaseSchema($this->em);
  }

  public function uninstallAction(){
    
  }
  
}