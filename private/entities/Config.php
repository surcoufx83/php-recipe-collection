<?php

namespace Surcouf\Cookbook;

use \DateInterval;
use Surcouf\Cookbook\Config\IconConfig;

if (!defined('CORE2'))
  exit;

final class Config {

  private $config, $icocfg, $responses;

  public function __construct() {
    global $Controller;
    $this->config = [
      'AdToken'                     => 'ca-pub-5505338479507158',
      'AllowRegistration'           => false,
      'ChecksumProvider'            => 'adler32',
      'ConsentCookieName'           => 'kbconsenttoken',
      'CronjobsEnabled'             => true,
      'DbDateFormat'                => 'Y-m-d',
      'DefaultDateFormat'           => 'd.m.Y',
      'DefaultDateFormatUi'         => 'd. F Y',
      'DefaultDateTimeFormat'       => 'd.m.Y H:i:s',
      'DefaultLongDateTimeFormat'   => 'l, d. F Y H:i:s',
      'DefaultDecimalsCount'        => 2,
      'DefaultDecimalsSeparator'    => ',',
      'DefaultListEntries'          => 10,
      'DefaultTimeFormat'           => 'H:i:s',
      'DefaultThousandsSeparator'   => '.',
      'HashProvider'                => 'crc32b',
      'LogCleanupTime'              => new DateInterval('P1M'),
      'LongTimeWarning'             => 180,
      'MaintenanceMode'             => file_exists(ROOT.DIRECTORY_SEPARATOR.'.maintenance.tmp'),
      'OAuth2Enabled'               => file_exists(DIR_BACKEND.DIRECTORY_SEPARATOR.'conf.oauth2.php'),
      'PageForceHttps'              => false,
      'PageHeader'                  => 'Kochbuch',
      'PageTitle'                   => 'Kochbuch',
      'PageUrls'                    => [
                                        'kochbuch.mogul.network',
                                        'localhost',
                                        '127.0.0.1',
                                       ],
      'PasswordCookieName'          => 'kbpasstoken',
      'PublicContact'               => 'Elias und Stefan',
      'PublicSignature'             => 'Kochbuch-Team',
      'PublicUrl'                   => 'kochbuch.mogul.network',
      'RecipeRatingClearance'       => new DateInterval('P30D'),
      'RecipeVisitedClearance'      => new DateInterval('P3D'),
      'SessionCookieName'           => 'kbsessiontoken',
      'SessionCleanupTime'          => new DateInterval('PT15M'),
      'SessionLongExpirationTime'   => new DateInterval('P1Y'),
      'SessionShortExpirationTime'  => new DateInterval('PT1H'),
      'UserCookieName'              => 'kbusertoken',
    ];
    $this->icocfg = new IconConfig();
    $this->responses = [
        1 => ['code' =>   1, 'message' => '', 'success' => true],
        2 => ['code' =>   2, 'message' => $Controller->l('response_noChanges'), 'success' => true],
        3 => ['code' =>   3, 'message' => $Controller->l('response_noResults'), 'success' => true],
       10 => ['code' =>  10, 'message' => $Controller->l('response_undefinedException'), 'success' => false],
       11 => ['code' =>  11, 'message' => $Controller->l('response_badRequestException'), 'success' => false],
       12 => ['code' =>  12, 'message' => $Controller->l('response_pageMovedException'), 'success' => false],
       30 => ['code' =>  30, 'message' => $Controller->l('response_loginFailed'), 'success' => false],
       31 => ['code' =>  31, 'message' => $Controller->l('response_loginSuccessfull'), 'success' => true],
       70 => ['code' =>  70, 'message' => $Controller->l('response_parameterException'), 'success' => false],
       71 => ['code' =>  71, 'message' => $Controller->l('response_functionNotFoundException'), 'success' => false],
       80 => ['code' =>  80, 'message' => $Controller->l('response_badArgumentsException'), 'success' => false],
       90 => ['code' =>  90, 'message' => $Controller->l('response_validationSucceeded'), 'success' => true],
       91 => ['code' =>  91, 'message' => $Controller->l('response_validationFailed'), 'success' => false],
      100 => ['code' => 100, 'message' => $Controller->l('response_maintenanceException'), 'success' => false],
      110 => ['code' => 110, 'message' => $Controller->l('response_notAuthenticatedException'), 'success' => false],
      111 => ['code' => 111, 'message' => $Controller->l('response_noApiKeyException'), 'success' => false],
      120 => ['code' => 120, 'message' => $Controller->l('response_insufficientPermissionException'), 'success' => false],
      201 => ['code' => 201, 'message' => $Controller->l('response_dbStmtException'), 'success' => false],
      202 => ['code' => 202, 'message' => $Controller->l('response_dbInsertException'), 'success' => false],
      203 => ['code' => 203, 'message' => $Controller->l('response_dbUpdateException'), 'success' => false],
      204 => ['code' => 204, 'message' => $Controller->l('response_dbSelectException'), 'success' => false],
      210 => ['code' => 210, 'message' => $Controller->l('response_sendMailFailed'), 'success' => false],
    ];
  }

  public function __call(string $methodName, array $params) {
    return array_key_exists($methodName, $this->config) ? $this->config[$methodName] : null;
  }

  public function __get(string $propertyName) {
    return array_key_exists($propertyName, $this->config) ? $this->config[$propertyName] : null;
  }

  public function getResponseArray(int $responseCode) : array {
    return array_key_exists($responseCode, $this->responses) ? $this->responses[$responseCode] : $this->responses[10];
  }

  public function Icons() : IconConfig {
    return $this->icocfg;
  }

}
