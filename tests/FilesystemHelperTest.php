<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\PhpArchive\Helper\FilesystemHelper;

require_once realpath(__DIR__.'/../private/entities/Helper/IFilesystemHelper.php');
require_once realpath(__DIR__.'/../private/entities/Helper/FilesystemHelper.php');

/**
 * @covers FilesystemHelper::<public>
 */
class FilesystemHelperTest extends TestCase
{

  /** @var string */
  private $tempFile = null;

  protected function setUp() : void {
    parent::setUp();
    $this->tempFile = __DIR__.DIRECTORY_SEPARATOR.'~FilesystemHelper-testfile';
    if (file_exists($this->tempFile))
      unlink($this->tempFile);
  }

  /**
   * @covers FilesystemHelper::file_exists
   */
  public function testFile_exists() {
    $this->assertFalse(FilesystemHelper::file_exists($this->tempFile));
  }

  /**
   * @covers FilesystemHelper::file_put_contents
   * @depends testFile_exists
   */
  public function testFile_put_contents() {
    $this->assertEquals(6, FilesystemHelper::file_put_contents($this->tempFile, 'foobar'));
    if (FilesystemHelper::file_exists($this->tempFile))
      unlink($this->tempFile);
  }

  /**
   * @covers FilesystemHelper::paths_combine
   */
  public function testPaths_combine() {
    $test = 'foo'.DIRECTORY_SEPARATOR.'bar';
    $this->assertEquals($test, FilesystemHelper::paths_combine('foo', 'bar'));
    $this->assertEquals($this->tempFile, FilesystemHelper::paths_combine(__DIR__, '~FilesystemHelper-testfile'));
  }

}
