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

  /**
   * @covers Formatter::min_format
   * @dataProvider min_formatDataProvider
   */
  public function testMin_format($value, $langstr, $expected) {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->expects($this->once())
               ->method('l')
               ->with(
                 $langstr,
                 $expected
               )
               ->willReturn($expected);
    $this->assertEquals($expected, Formatter::min_format($value));
  }

  /**
   * @covers Formatter::t
   * @dataProvider tDataProvider
   */
  public function testT($value, $singular, $plural, $flags, $separator, $expected) {
    global $Controller;
    $Controller = $this->createStub(ControllerInterface::class);
    $stubConfig = $this->createStub(ConfigInterface::class);
    $Controller->method('Config')->willReturn($stubConfig);
    $stubConfig->method('__call')->willReturn('');
    $this->assertEquals($expected, Formatter::t($value, $singular, $plural, $flags, $separator));
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

  public function min_formatDataProvider() {
    return [
      [1, 'common_duration_minutes', '1'],
      [59, 'common_duration_minutes', '59'],
      [60, 'common_duration_hours', '1'],
      [90, 'common_duration_hours', '1.5'],
      [1439, 'common_duration_hours', '23.5'],
      [1440, 'common_duration_days', '1'],
      [2880, 'common_duration_days', '2'],
      [3600, 'common_duration_days', '2.5'],
      [43200, 'common_duration_days', '30'],
    ];
  }

  public function tDataProvider() {
    return [
      [0, 'foo', 'foos', 0, ' ', 'foos'],
      [1, 'foo', 'foos', 0, ' ', 'foo'],
      [2, 'foo', 'foos', 0, ' ', 'foos'],
      [0, 'bar', 'bars', 1, ' ', '0 bars'],
      [1, 'bar', 'bars', 1, ' ', '1 bar'],
      [2, 'bar', 'bars', 1, ' ', '2 bars'],
      [0, 'foo', 'foos', 2, ' ', 'foos 0'],
      [1, 'foo', 'foos', 2, ' ', 'foo 1'],
      [2, 'foo', 'foos', 2, ' ', 'foos 2'],
      [0, 'bar', 'bars', 3, '@', '0@bars@0'],
      [1, 'bar', 'bars', 3, '@', '1@bar@1'],
      [2, 'bar', 'bars', 3, '@', '2@bars@2'],
    ];
  }

}
