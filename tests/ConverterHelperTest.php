<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Helper\ConverterHelper;


require_once realpath(__DIR__.'/../private/entities/Helper/IConverterHelper.php');
require_once realpath(__DIR__.'/../private/entities/Helper/ConverterHelper.php');

/**
 * @covers ConverterHelper::<public>
 */
class ConverterHelperTest extends TestCase
{

  protected function setUp() : void {
    parent::setUp();
  }

  /**
   * @covers ConverterHelper::bool_to_str
   */
  public function testBool_to_str() {
    $this->assertEquals('true', ConverterHelper::bool_to_str(true));
    $this->assertEquals('false', ConverterHelper::bool_to_str(false));
  }

  /**
   * @covers ConverterHelper::to_bool
   * @dataProvider toBoolDataProvider
   */
  public function testTo_bool($test, $expected) {
    $this->assertEquals($expected, ConverterHelper::to_bool($test));
  }

  public function toBoolDataProvider() {
    return [
      [true, true],
      [1, true],
      ['1', true],
      ['true', true],
      ['yes', true],
      [false, false],
      [0, false],
      [1.0, false],
      ['foo', false],
      ['false', false],
      ['no', false],
      [array(), false],
      [new DateTime(), false],
    ];
  }

}
