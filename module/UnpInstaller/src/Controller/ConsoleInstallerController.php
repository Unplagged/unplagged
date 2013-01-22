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
use UnpInstaller\InstallerAware;
use Zend\Console\Prompt\Confirm;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Select;

/**
 * Provides a console interface mostly for helping during the development of Unplagged.
 */
class ConsoleInstallerController extends BaseController implements InstallerAware{

  private $outputStream = null;
  private $installer = null;
  
  public function __construct(){
    $this->outputStream = STDOUT;
  }
  
  public function setInstaller(Installer $installer){
    $this->installer = $installer;
    $this->installer->setOutputStream(STDOUT);
  }

  public function setOutputStream($stream){
      $this->outputStream = $stream;
  }
  
  /**
   * Command line action, that deletes all tables from the database.
   * 
   * @codeCoverageIgnore Seems untestable because of requested console input
   */
  public function deleteDatabaseSchemaAction(){
    if(Confirm::prompt('This will delete all saved data! Are you sure you want to continue? [y/n]', 'y', 'n')){
      $this->installer->deleteDatabaseSchema($this->em);
    }
  }

  /**
   * Questions the user for all necessary information about the database connection and notifies if they would work.
   * 
   * @codeCoverageIgnore Seems untestable because of requested console input
   */
  public function checkDatabaseConnectionAction(){
    $options = array(
        '1'=>'MySQL',
        '2'=>'Sqlite',
    );
    $answer = Select::prompt('Please select your database type.', $options, false, false);
    $config = null;
    switch($answer){
      case '1':
        $config = $this->questionMySqlParameters();
        break;
      case '2':
        $config = $this->questionSqliteParamters();
        break;
    }
    $this->installer->checkDatabaseConnection($config);
  }

  /**
   * Asks the user for all necessary parameters to establish a connection with a MySQL database.
   * 
   * @return array
   * 
   * @codeCoverageIgnore Seems untestable because of requested console input
   */
  private function questionMySqlParameters(){
    $config = array();
    $config['driverClass'] = 'Doctrine\DBAL\Driver\PDOMySql\Driver';

    $host = $this->promptLine('What is the hostname?(defaults to: localhost)');
    $port = $this->promptLine('What is the port?(defaults to: 3306)');
    $user = $this->promptLine('What is the username?(defaults to: unplagged)');
    $password = $this->promptLine('What is the password?');
    $dbname = $this->promptLine('What is the name of the database?(defaults to: unplagged)');

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
   * Asks the user for all necessary parameters to establish a connection with a Sqlite database.
   * 
   * @return array
   * 
   * @codeCoverageIgnore Seems untestable because of requested console input
   */
  private function questionSqliteParamters(){
    $config = array();
    $config['driverClass'] = 'Doctrine\DBAL\Driver\PDOSqlite\Driver';

    $path = $this->promptLine('What is the path of the database file?', false);

    $config['params'] = array(
        'path'=>$path,
    );
    return $config;
  }

  /**
   * @param string $questionText
   * @param bool $empty
   * @return string
   * 
   * @codeCoverageIgnore Seems untestable because of requested console input
   */
  private function promptLine($questionText, $empty = true){
     return Line::prompt($questionText, $empty, 100);
  }
  
  /**
   * Command line action that updates the database schema from the model files.
   */
  public function updateDatabaseSchemaAction(){
    $this->installer->updateDatabaseSchema($this->em);
  }

  /**
   * @codeCoverageIgnore Seems untestable because of requested console input
   */
  public function uninstallAction(){
    if(Confirm::prompt('This will completely uninstall the application including all stored data! Are you sure you want to continue? [y/n]', 'y', 'n')){
      $this->installer = $this->createInstaller();
      $this->installer->uninstall($this->em);
    }
  }
  
}