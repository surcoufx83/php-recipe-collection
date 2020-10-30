<?php

use PHPUnit\Framework\TestCase;
use Surcouf\Cookbook\ConfigInterface;
use Surcouf\Cookbook\ControllerInterface;
use Surcouf\Cookbook\Helper\HashHelper;

require_once realpath(__DIR__.'/../private/entities/ConfigInterface.php');
require_once realpath(__DIR__.'/../private/entities/ControllerInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/HashHelperInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/HashHelper.php');

/**
 * @covers HashHelper::<public>
 */
class HashHelperTest extends TestCase
{

  /**
   * @covers HashHelper::generate_token
   * @dataProvider tokenDataProvider
   */
  public function testGenerate_token(int $length, int $expected) {
    $result = HashHelper::generate_token($length);
    $this->assertIsString($result);
    $this->assertEquals($expected, strlen($result));
  }

  /**
   * @covers HashHelper::getChecksumAlgo
   */
  public function testGetChecksumAlgo() {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->method('__get')->willReturn('adler32');
    $this->assertEquals('adler32', HashHelper::getChecksumAlgo());
  }

  /**
   * @covers HashHelper::getHashAlgo
   */
  public function testGetHashAlgo() {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->method('__get')->willReturn('crc32b');
    $this->assertEquals('crc32b', HashHelper::getHashAlgo());
  }

  /**
   * @covers HashHelper::hash
   * @depends testGetHashAlgo
   */
  public function testHash() {
    $this->assertEquals('8c736521', HashHelper::hash('foo', 'crc32b'));
    $this->assertEquals('76ff8caa', HashHelper::hash('bar', 'crc32b'));
  }

  /**
   * @covers HashHelper::hash
   * @depends testHash
   * @dataProvider hashDataProvider
   */
  public function testHashwithParams(string $input, string $algo, string $expected) {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->method('__get')->willReturn($algo);
    $this->assertEquals($expected, HashHelper::hash($input));
    $this->assertEquals($expected, HashHelper::hash($input, $algo));
  }

  public function tokenDataProvider() {
    return [
      [0, 64],
      [8, 64],
      [9, 18],
      [16, 32],
      [32, 64],
      [256, 512],
    ];
  }

  public function hashDataProvider() {
    return [
      ['foo', 'adler32', '02820145'],
      ['foo', 'crc32', 'a5c4fe49'],
      ['foo', 'md5', 'acbd18db4cc2f85cedef654fccc4a4d8'],
    ];
  }

}
