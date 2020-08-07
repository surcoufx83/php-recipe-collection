<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\PhpArchive\Controller;
use Surcouf\PhpArchive\Helper\HashHelper;

require_once realpath(__DIR__.'/../private/entities/IController.php');
require_once realpath(__DIR__.'/../private/entities/Controller.php');
require_once realpath(__DIR__.'/../private/entities/Helper/IHashHelper.php');
require_once realpath(__DIR__.'/../private/entities/Helper/HashHelper.php');

/**
 * @covers HashHelper::<public>
 */
class HashHelperTest extends TestCase
{

  /** @var Controller|MockObject */
  private $Controller;

  protected function setUp() : void {
    parent::setUp();
    $this->Controller = $this->getMockBuilder(Controller::class)
      ->disableOriginalConstructor()
      ->disableOriginalConstructor()
      ->getMock();
  }

  /**
   * @covers HashHelper::generate_token
   */
  public function testGenerate_token() {
    $this->assertIsString(HashHelper::generate_token());
  }

  /**
   * @covers HashHelper::getHashAlgo
   */
  public function testGetHashAlgo() {
    $this->markTestSkipped(
      'Waiting for Config redesign'
    );
  }

  /**
   * @covers HashHelper::hash
   * @depends testGetHashAlgo
   */
  public function testHash() {
    $this->markTestSkipped(
      'Waiting for Config redesign'
    );
  }

}
