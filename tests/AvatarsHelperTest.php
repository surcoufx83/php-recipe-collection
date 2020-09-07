<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Helper\AvatarsHelper;
use Surcouf\Cookbook\Helper\FilesystemHelper;

require_once realpath(__DIR__.'/../private/entities/Helper/IFilesystemHelper.php');
require_once realpath(__DIR__.'/../private/entities/Helper/FilesystemHelper.php');
require_once realpath(__DIR__.'/../private/entities/Helper/AvatarsHelperInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/AvatarsHelper.php');

/**
 * @covers AvatarsHelper::<public>
 */
class AvatarsHelperTest extends TestCase
{

  protected function setUp() : void {
    parent::setUp();
  }

  /**
   * @covers AvatarsHelper::createAvatar
   */
  public function testCreateAvatar() {
    $this->markTestSkipped(
      'Requires redesign of config'
    );
  }

  /**
   * @covers AvatarsHelper::exists
   */
  public function testExists() {
    $this->assertFalse(AvatarsHelper::exists('foo'));
  }

}
