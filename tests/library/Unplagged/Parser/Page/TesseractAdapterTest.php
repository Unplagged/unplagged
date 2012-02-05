<?php
/**
 * File for class {@link TesseractAdapterTest}.
 */

/**
 *
 */
class TesseractAdapterTest extends PHPUnit_Framework_TestCase{
  
  public function testCheckTesseractCallIsWorking(){
    $this->assertTrue(Unplagged_Parser_Page_TesseractAdapter::checkTesseract());
  }
  
  public function testCheckTesseractWithWrongCommand(){
    $this->assertFalse(Unplagged_Parser_Page_TesseractAdapter::checkTesseract('WrongCommand'));
  }
  
  public function testInputFileMustExist(){
    $this->setExpectedException('InvalidArgumentException');
    $tesseractAdapter = new Unplagged_Parser_Page_TesseractAdapter('', '', '');
  }
  
  public function testOutputFileNameMustBeSpecified(){
    $this->setExpectedException('InvalidArgumentException');
    new Unplagged_Parser_Page_TesseractAdapter(realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR, '');
  }
  
  public function testOutputPathMustBeWriteable(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;
    $testFileName = 'p13a';
    $unwriteableDir = 'unwriteableDir';
    
    $this->setExpectedException('InvalidArgumentException');
    $tesseractAdapter = new Unplagged_Parser_Page_TesseractAdapter($resourcesPath . $testFileName . '.tif', $unwriteableDir . DIRECTORY_SEPARATOR . $testFileName); 
  }
  
  public function testTxtFileGetsCreated(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;

    $testFileName = 'p13a';
    $outputFile = $resourcesPath . $testFileName;
    $outputFileName = $outputFile . '.txt';
    
    //make sure no old txt file is present
    if(file_exists($outputFileName)){
      unlink($outputFileName);
    }
    
    $tesseractAdapter = new Unplagged_Parser_Page_TesseractAdapter($resourcesPath . $testFileName . '.tif', $outputFile);
    $tesseractAdapter->execute();
    
    $this->assertFileExists($resourcesPath . $testFileName . '.txt', 'Please make sure Tesseract is installed and on the include path.');
    
    
    //delete the newly created txt file
    unlink($resourcesPath . $testFileName . '.txt');
  }
}
?>
