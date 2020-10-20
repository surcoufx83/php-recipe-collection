<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Config;
use Surcouf\Cookbook\ControllerInterface;
use Surcouf\Cookbook\Config\IconConfigInterface;

if (!defined('DS'))
  define('DS', DIRECTORY_SEPARATOR);

define('ROOT', realpath(__DIR__.DS.'/..'));
define('DIR_BACKEND', realpath(ROOT.DS.'private'.DS.'backend'));

require_once realpath(__DIR__.'/../private/entities/ControllerInterface.php');
require_once realpath(__DIR__.'/../private/entities/ConfigInterface.php');
require_once realpath(__DIR__.'/../private/entities/Config/IconConfigInterface.php');
require_once realpath(__DIR__.'/../private/entities/Config/IconConfig.php');
require_once realpath(__DIR__.'/../private/entities/Config.php');

/**
 * @covers Config::<public>
 */
class ConfigTest extends TestCase
{

  /**
   * Config $cfg
   */
  protected $cfg;

  protected function setUp() : void {
    global $Controller;
    parent::setUp();
    $Controller = $this->createStub(ControllerInterface::class);
    $Controller->expects($this->any())->method('l')->willReturn('foo');
    $this->cfg = new Config();
  }

  /**
   * @covers Config::__call()
   * @dataProvider callDataProvider
   */
  public function test__call(string $key, $expected) {
    $this->assertEquals($expected, $this->cfg->$key());
  }

  /**
   * @covers Config::__get()
   * @dataProvider callDataProvider
   */
  public function test__get(string $key, $expected) {
    $this->assertEquals($expected, $this->cfg->$key);
  }

  /**
   * @covers Config::getResponseArray()
   * @dataProvider getResponseArrayDataProvider
   */
  public function testGetResponseArray(int $key, $expected) {
    $this->assertEquals($expected, $this->cfg->getResponseArray($key));
  }

  /**
   * @covers Config::Icons()
   */
  public function testIcons() {
    $this->assertInstanceOf(IconConfigInterface::class, $this->cfg->Icons());
  }

  public function callDataProvider() {
    return [
      ['AllowRegistration', false],
      ['ChecksumProvider', 'adler32'],
      ['ConsentCookieName', 'kbconsenttoken'],
      ['CronjobsEnabled', true],
      ['DbDateFormat', 'Y-m-d'],
      ['DefaultDateFormat', 'd.m.Y'],
      ['DefaultDateFormatUi', 'd. F Y'],
      ['DefaultDateTimeFormat', 'd.m.Y H:i:s'],
      ['DefaultLongDateTimeFormat', 'l, d. F Y H:i:s'],
      ['DefaultDecimalsCount', 2],
      ['DefaultDecimalsSeparator', ','],
      ['DefaultListEntries', 15],
      ['DefaultTimeFormat', 'H:i:s'],
      ['DefaultThousandsSeparator', '.'],
      ['HashProvider', 'crc32b'],
      ['LogCleanupTime', new DateInterval('P1M')],
      ['LongTimeWarning', 180],
      ['MaintenanceMode', file_exists(ROOT.DS.'.maintenance.tmp')],
      ['OAuth2Enabled', file_exists(DIR_BACKEND.DS.'conf.oauth2.php')],
      ['PageForceHttps', false],
      ['PageHeader', 'Kochbuch'],
      ['PageTitle', 'Kochbuch'],
      ['PageUrls', [
                    'kochbuch.mogul.network',
                    'localhost',
                    '127.0.0.1',
                   ]],
      ['PasswordCookieName', 'kbpasstoken'],
      ['PublicContact', 'Elias und Stefan'],
      ['PublicSignature', 'Kochbuch-Team'],
      ['PublicUrl', 'kochbuch.mogul.network'],
      ['RecipeRatingClearance', new DateInterval('P30D')],
      ['RecipeVisitedClearance', new DateInterval('P1DT12H')],
      ['SessionCookieName', 'kbsessiontoken'],
      ['SessionCleanupTime', new DateInterval('PT15M')],
      ['SessionLongExpirationTime', new DateInterval('P1Y')],
      ['SessionShortExpirationTime', new DateInterval('PT1H')],
      ['UserCookieName', 'kbusertoken'],
    ];
  }

  public function getResponseArrayDataProvider() {
    return [
    [  1, ['code' =>   1, 'message' => '', 'success' => true]],
    [  2, ['code' =>   2, 'message' => 'foo', 'success' => true]],
    [  3, ['code' =>   3, 'message' => 'foo', 'success' => true]],
    [ 10, ['code' =>  10, 'message' => 'foo', 'success' => false]],
    [ 11, ['code' =>  11, 'message' => 'foo', 'success' => false]],
    [ 12, ['code' =>  12, 'message' => 'foo', 'success' => false]],
    [ 30, ['code' =>  30, 'message' => 'foo', 'success' => false]],
    [ 31, ['code' =>  31, 'message' => 'foo', 'success' => true]],
    [ 70, ['code' =>  70, 'message' => 'foo', 'success' => false]],
    [ 71, ['code' =>  71, 'message' => 'foo', 'success' => false]],
    [ 80, ['code' =>  80, 'message' => 'foo', 'success' => false]],
    [ 90, ['code' =>  90, 'message' => 'foo', 'success' => true]],
    [ 91, ['code' =>  91, 'message' => 'foo', 'success' => false]],
    [100, ['code' => 100, 'message' => 'foo', 'success' => false]],
    [110, ['code' => 110, 'message' => 'foo', 'success' => false]],
    [111, ['code' => 111, 'message' => 'foo', 'success' => false]],
    [120, ['code' => 120, 'message' => 'foo', 'success' => false]],
    [201, ['code' => 201, 'message' => 'foo', 'success' => false]],
    [202, ['code' => 202, 'message' => 'foo', 'success' => false]],
    [203, ['code' => 203, 'message' => 'foo', 'success' => false]],
    [204, ['code' => 204, 'message' => 'foo', 'success' => false]],
    [210, ['code' => 210, 'message' => 'foo', 'success' => false]],
    ];
  }

}
