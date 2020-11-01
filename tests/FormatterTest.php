<?php

use PHPUnit\Framework\TestCase;
use Surcouf\Cookbook\Helper\Formatter;
use Surcouf\Cookbook\ConfigInterface;
use Surcouf\Cookbook\ControllerInterface;

require_once realpath(__DIR__.'/../private/entities/ConfigInterface.php');
require_once realpath(__DIR__.'/../private/entities/ControllerInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/FlagsInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/Flags.php');
require_once realpath(__DIR__.'/../private/entities/Helper/FormatterInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/Formatter.php');

/**
 * @covers  Formatter::<public>
 */
class FormatterTest extends TestCase
{

  /**
   * @covers Formatter::byte_format
   * @depends testFloat_format
   * @dataProvider byte_formatDataProvider
   */
  public function testByte_format($value, $precission, $expected) {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->expects($this->exactly(3))
               ->method('__call')
               ->will($this->onConsecutiveCalls(2, '.', ''));
    $this->assertEquals($expected, Formatter::byte_format($value, $precission));
  }

  /**
   * @covers Formatter::date_format
   * @dataProvider date_formatDataProvider
   */
  public function testDate_format($value, $format, $expected) {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->method('__call')->willReturn('d. F Y');
    $this->assertEquals($expected, Formatter::date_format($value, $format));
  }

  /**
   * @covers Formatter::float_format
   * @dataProvider float_formatDataProvider
   */
  public function testFloat_format($value, $precission, $expected) {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->expects($this->exactly(3))
               ->method('__call')
               ->will($this->onConsecutiveCalls(2, '.', ''));
    $this->assertEquals($expected, Formatter::float_format($value, $precission));
  }

  /**
   * @covers Formatter::int_format
   * @dataProvider int_formatDataProvider
   */
  public function testInt_format($value, $expected) {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->method('__call')->willReturn('');
    $this->assertEquals($expected, Formatter::int_format($value));
  }

  public function byte_formatDataProvider() {
    return [
      [               0, -1,      '0 B'],
      [               0,  1,    '0.0 B'],
      [            1024, -1,   '1.0 KB'],
      [            1024,  0,     '1 KB'],
      [         1048576, -1,   '1.0 MB'],
      [         1048576,  0,     '1 MB'],
      [         1649664,  1,   '1.6 MB'],
      [         1649664,  2,  '1.57 MB'],
      [         1649664,  3, '1.573 MB'],
      [      1073741824, -1,   '1.0 GB'],
      [   1099511627776, -1,   '1.0 TB'],
      [1125899906842624, -1,   '1.0 PB'],
    ];
  }

  public function float_formatDataProvider() {
    return [
      [0.0, -1, '0.00'],
      [0.0,  0, '0'],
      [0.0,  1, '0.0'],
      [-10.0, -1, '-10.00'],
      [-10.0,  0, '-10'],
      [-10.0,  1, '-10.0'],
      [10.0, -1, '10.00'],
      [10.0,  0, '10'],
      [10.0,  1, '10.0'],
      [10000000.1234, -1, '10000000.12'],
      [10000000.1234,  0, '10000000'],
      [10000000.1234,  1, '10000000.1'],
      [10000000.156, -1, '10000000.16'],
      [10000000.156,  0, '10000000'],
      [10000000.156,  1, '10000000.2'],
    ];
  }

  public function date_formatDataProvider() {
    return [
      [null, null, (new DateTime())->format('d. F Y')],
      [new DateTime('2020-01-01'), null, '01. January 2020'],
      [new DateTime('2020-01-01'), 'Y-m-d', '2020-01-01'],
    ];
  }

  public function int_formatDataProvider() {
    return [
      [0, '0'],
      [1, '1'],
      [-1, '-1'],
      [1000, '1000'],
      [-1000, '-1000'],
    ];
  }

}
