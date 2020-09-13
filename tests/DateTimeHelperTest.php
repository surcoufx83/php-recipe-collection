<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Helper\DateTimeHelper;


require_once realpath(__DIR__.'/../private/entities/Helper/DateTimeHelperInterface.php');
require_once realpath(__DIR__.'/../private/entities/Helper/DateTimeHelper.php');

/**
 * @covers DateTimeHelper::<public>
 */
class DateTimeHelperTest extends TestCase
{

  protected function setUp() : void {
    parent::setUp();
  }

  /**
   * @covers DateTimeHelper::dateInterval2IsoFormat
   * @dataProvider intervalProvider
   */
  public function testDateInterval2IsoFormat($interval, $output) {
    $this->assertEquals($output, DateTimeHelper::dateInterval2IsoFormat($interval));
  }

  public function intervalProvider() {
    return [
      [new DateInterval('PT0S'), 'PT0S'],
      [new DateInterval('PT1M1S'), 'PT1M1S'],
      [new DateInterval('PT1H1M1S'), 'PT1H1M1S'],
      [new DateInterval('PT1H0M1S'), 'PT1H1S'],
      [new DateInterval('P1DT1H1M1S'), 'P1DT1H1M1S'],
      [new DateInterval('P0DT1H1M1S'), 'PT1H1M1S'],
      [new DateInterval('P1M1DT1H1M1S'), 'P1M1DT1H1M1S'],
      [new DateInterval('P1M0DT1H1M1S'), 'P1MT1H1M1S'],
      [new DateInterval('P0M1DT1H1M1S'), 'P1DT1H1M1S'],
      [new DateInterval('P1Y1M1DT1H1M1S'), 'P1Y1M1DT1H1M1S'],
      [DateInterval::createFromDateString('-89 days'), 'P-89D'],
    ];
  }

}
