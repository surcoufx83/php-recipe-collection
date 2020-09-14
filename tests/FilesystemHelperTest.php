<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Helper\FilesystemHelper;

define('DS', DIRECTORY_SEPARATOR);

require_once realpath(__DIR__.'/../private/entities/Helper/FilesystemHelperInterface.php');
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
    $this->tempFile = __DIR__.DS.'~FilesystemHelper-testfile';
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
   * @dataProvider pathsCombineDataProvider
   */
  public function testPaths_combine(string $e1, string $e2, ?string $e3 = null, ?string $e4 = null, string $expected) {
    $this->assertEquals($expected, FilesystemHelper::paths_combine($e1, $e2, $e3, $e4));
  }

  public function pathsCombineDataProvider() {
    return [
      [__DIR__, '~FilesystemHelper-testfile', null, null, __DIR__.DS.'~FilesystemHelper-testfile'],
      [__DIR__, 'foo', null, null, __DIR__.DS.'foo'],
      [__DIR__, 'foo', 'bar', null, __DIR__.DS.'foo'.DS.'bar'],
      [__DIR__, 'foo', 'bar', 'foobar', __DIR__.DS.'foo'.DS.'bar'.DS.'foobar'],
      ['foo', 'bar', null, null, 'foo'.DS.'bar'],
    ];
  }

}
