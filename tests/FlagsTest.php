<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Helper\Flags;


require_once realpath(__DIR__.'/../private/entities/Helper/FlagsInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/Flags.php');

/**
 * @covers  Flags::<public>
 */
class FlagsTest extends TestCase
{

  protected function setUp() : void {
    parent::setUp();
  }

  /**
   * @covers Flags::add_flag
   */
  public function testAdd_flag() {
    $value = 0;
    Flags::add_flag($value, 1);
    $this->assertEquals(1, $value);
    Flags::add_flag($value, 1); // no change, shall stay 1
    $this->assertEquals(1, $value);
    Flags::add_flag($value, 3); // shall add flag 2 but not 1 (sum shall be 3)
    $this->assertEquals(3, $value);
    Flags::add_flag($value, 10); // had 1, 2; adding 8 and 2 (=10) -> 11
    $this->assertEquals(11, $value);
    Flags::add_flag($value, 255);
    $this->assertEquals(255, $value);
  }

  /**
   * @covers Flags::has_flag
   * @dataProvider has_flagDataProvider
   */
  public function testHas_flag($value, $flag, $expected) {
    $this->assertEquals($expected, Flags::has_flag($value, $flag));
  }

  /**
   * @covers Flags::remove_flag
   */
  public function testRemove_flag() {
    $value = 255;
    Flags::remove_flag($value, 1);
    $this->assertEquals(254, $value);
    Flags::remove_flag($value, 256); // 256 is not in 254, so no changes
    $this->assertEquals(254, $value);
    Flags::remove_flag($value, 255); // removes any flag but 1 (that was removed before)
    $this->assertEquals(0, $value);
  }

  public function has_flagDataProvider() {
    return [
      [0, 0, true],
      [0, 1, false],
      [1, 1, true],
      [1, 2, false],
      [2, 2, true],
      [2, 1, false],
      [3, 1, true],
      [3, 2, true],
      [255, 1, true],
      [255, 2, true],
      [255, 4, true],
      [255, 8, true],
      [255, 16, true],
      [255, 32, true],
      [255, 64, true],
      [255, 128, true],
      [255, 3, true],
      [255, 10, true],
      [256, 1, false],
      [256, 256, true],
    ];
  }

}
