<?php

/**
 * File for class {@link ImagemagickAdapterTest}.
 */

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class ImagemagickAdapterTest extends PHPUnit_Framework_TestCase{
  
  public function testConstructorArgumentsMustBeValid()
  {
    $this->setExpectedException('InvalidArgumentException');
    
    new Unplagged_Parser_Document_ImagemagickAdapter('', '');
  }
  
  public function testFileGetsParse(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;

    $testFileName = 'stock_gs200';
    $inputFile = $resourcesPath . $testFileName . '.jpg';
    $outputFile = $resourcesPath . $testFileName . '.tif';
    
    //make sure no old file is present
    if(file_exists($outputFile)){
      unlink($outputFile);
    }
        
    $adapter = new Unplagged_Parser_Document_ImagemagickAdapter($inputFile, $outputFile);
    $adapter->execute();
    
    $this->assertFileExists($outputFile, 'Please make sure Imagemagick is installed and on the include path.');
    
    //delete the newly created file
    unlink($outputFile);    
  }
  
}
?>
