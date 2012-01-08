<?php
/**
 * File for class {@link TesseractAdapterTest}.
 */

/**
 *
 */
class TesseractAdapterTest extends PHPUnit_Framework_TestCase{
  
  public function testCheckTesseractCallIsWorking(){
    $this->assertTrue(Unplagged_Parser_TesseractAdapter::checkTesseract());
  }
  
  public function testCheckTesseractWithWrongCommand(){
    $this->assertFalse(Unplagged_Parser_TesseractAdapter::checkTesseract('WrongCommand'));
  }
  
  public function testInputFileMustExist(){
    $this->setExpectedException('InvalidArgumentException');
    $tesseractAdapter = new Unplagged_Parser_TesseractAdapter('', '', '');
  }
  
  public function testOutputFileNameMustBeSpecified(){
    $this->setExpectedException('InvalidArgumentException');
    new Unplagged_Parser_TesseractAdapter(realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR, 'p13a.tif', '');
  }
  
  public function testTxtFileGetsCreated(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;
   
    $testFileName = 'p13a';
    $outputFileName = $resourcesPath . $testFileName . '.txt';
    
    //make sure no old txt file is present
    if(file_exists($outputFileName)){
      unlink($outputFileName);
    }
    
    $tesseractAdapter = new Unplagged_Parser_TesseractAdapter($resourcesPath, $testFileName . '.tif', $testFileName);
    $tesseractAdapter->execute();
    
    $this->assertFileExists($resourcesPath . $testFileName . '.txt', 'Please make sure Tesseract is installed and on the include path.');
    
    
    //delete the newly created txt file
    unlink($resourcesPath . $testFileName . '.txt');
  }
}
?>
