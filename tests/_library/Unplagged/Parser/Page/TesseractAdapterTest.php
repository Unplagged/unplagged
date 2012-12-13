<?php
/**
 * File for class {@link TesseractAdapterTest}.
 */

require_once '../library/Unplagged/Parser/Page/TesseractAdapter.php';

/**
 *
 */
class TesseractAdapterTest extends PHPUnit_Framework_TestCase{
  
  public function testTesseractIsInstalled(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;

    $testFileName = 'p13a';
    $outputFile = $resourcesPath . $testFileName;
    $outputFileName = $outputFile . '.txt';
    
    //make sure no old txt file is present
    if(file_exists($outputFileName)){
      unlink($outputFileName);
    }
    
    $parser = new Unplagged_Parser_Page_TesseractAdapter($resourcesPath . $testFileName . '.tif', $outputFile, 'en', Zend_Registry::get('config')->parser->tesseractPath);
    
    $this->assertTrue($parser->checkTesseract());
  }
  
  public function testCheckTesseractCallWithWrongPathIsFalse(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;

    $testFileName = 'p13a';
    $outputFile = $resourcesPath . $testFileName;
    $outputFileName = $outputFile . '.txt';
    
    //make sure no old txt file is present
    if(file_exists($outputFileName)){
      unlink($outputFileName);
    }
    $parser = new Unplagged_Parser_Page_TesseractAdapter($resourcesPath . $testFileName . '.tif', $outputFile, 'en', '/wrong/path');
    
    $this->assertFalse($parser->checkTesseract());
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
  
  public function testUnknownLanguageFallsBackToDefaultLanguage(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;

    $testFileName = 'p13a';
    $outputFile = $resourcesPath . $testFileName;
    $outputFileName = $outputFile . '.txt';
    
    //make sure no old txt file is present
    if(file_exists($outputFileName)){
      unlink($outputFileName);
    }
    
    $tesseractAdapter = new Unplagged_Parser_Page_TesseractAdapter($resourcesPath . $testFileName . '.tif', $outputFile, 'some unknown-lanugage', Zend_Registry::get('config')->parser->tesseractPath);
    $tesseractAdapter->execute();
    
    $this->assertFileExists($resourcesPath . $testFileName . '.txt', 'Please make sure Tesseract is installed and on the include path.');
    
    
    //delete the newly created txt file
    unlink($resourcesPath . $testFileName . '.txt');
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
    
    $tesseractAdapter = new Unplagged_Parser_Page_TesseractAdapter($resourcesPath . $testFileName . '.tif', $outputFile, 'en', Zend_Registry::get('config')->parser->tesseractPath);
    $this->assertTrue($tesseractAdapter->execute());
    
    $this->assertFileExists($resourcesPath . $testFileName . '.txt', 'Please make sure Tesseract is installed and on the include path.');
    
    
    //delete the newly created txt file
    unlink($resourcesPath . $testFileName . '.txt');
  }
  
  public function testExecuteReturnsFalseOnError(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;

    $testFileName = 'empty_file';
    $outputFile = $resourcesPath . $testFileName;
    $outputFileName = $outputFile . '.txt';
    
    //make sure no old txt file is present
    if(file_exists($outputFileName)){
      unlink($outputFileName);
    }
    
    $tesseractAdapter = new Unplagged_Parser_Page_TesseractAdapter($resourcesPath . $testFileName . '.tiff', $outputFile, 'en', Zend_Registry::get('config')->parser->tesseractPath);
    
    $this->assertFalse($tesseractAdapter->execute());
  }
}
?>
