<?php
/**
 * File for class {@link TesseractAdapterTest}.
 */

include APPLICATION_PATH . DIRECTORY_SEPARATOR . 'ocr/TesseractAdapter.php';

/**
 *
 * @author Dominik Horb <dominik.horb@googlemail.com>
 */
class TesseractAdapterTest extends PHPUnit_Framework_TestCase{
  
  public function testArgumentsMustBeValid(){
    $this->setExpectedException('InvalidArgumentException');
    $tesseractAdapter = new TesseractAdapter('', '', '');
  }
  
  public function testTxtFileGetsCreated(){
    $resourcesPath = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'resources') . DIRECTORY_SEPARATOR;
   
    $testFileName = 'p13a';
    $outputFileName = $resourcesPath . $testFileName . '.txt';
    
    //make sure no old txt file is present
    if(file_exists($outputFileName)){
      unlink($outputFileName);
    }
    
    $tesseractAdapter = new TesseractAdapter($resourcesPath, $testFileName . '.tif', $testFileName);
    $tesseractAdapter->execute();
    
    $this->assertFileExists($resourcesPath . $testFileName . '.txt');
    
    //delete the newly created file
    unlink($resourcesPath . $testFileName . '.txt');
  }
}
?>
