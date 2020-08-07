<?php

namespace Surcouf\PhpArchive\Config;

use \DateInterval;
use \DateTime;
use Surcouf\PhpArchive;
use Surcouf\PhpArchive\Config\ConfigObject;
use Surcouf\PhpArchive\Config\Icon;
use Surcouf\PhpArchive\Mail\Account;

if (!defined('CORE2'))
  exit;

/**
 * The IConfigurationValue interface describes generally valid methods and
 * properties for all configuration parameters.
 */
interface IConfigurationValue {

  public function addChild(ConfigObject &$child) : ConfigObject;

  public function getArray() : ?array;
  public function getBool() : ?bool;
  public function getChild($n) : ?\Surcouf\PhpArchive\ConfigurationValue;
  public function getDateTime() : ?DateTime;
  public function getDescription() : string;
  public function getFloat() : ?float;
  public function getIcon(?string $cssClass=null, ?string $customStyle=null, ?string $id=null) : string;
  public function getIconObj() : ?Icon;
  public function getId() : int;
  public function getInt() : ?int;
  public function getLastUpdate() : ?DateTime;
  public function getMailAccount() : ?Account;
  public function getName() : string;
  public function getParentId() : ?int;
  public function getResponseCode() : ?array;
  public function getString() : ?string;
  public function getTimespan() : ?DateInterval;
  public function getType() : int;

  public function isEditable() : bool;

  public function setArray(array $newValue) : bool;
  public function setBoolean(bool $newValue) : bool;
  public function setDateTime(DateTime $newValue) : bool;
  public function setIcon(string $newSpace, string $newIcon) : bool;
  public function setInt(int $newValue) : bool;
  public function setFloat(float $newValue) : bool;
  public function setResponse(int $newCode, string $newMessage, bool $newResult) : bool;
  public function setString(string $newValue) : bool;
  public function setTimespan(DateInterval $newValue) : bool;

}
