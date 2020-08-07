<?php

namespace Surcouf\PhpArchive\Config;

use \ArrayObject;
use \DateInterval;
use \DateTime;
use Surcouf\PhpArchive\Config\EConfigurationType;
use Surcouf\PhpArchive\Config\IConfigurationValue;
use Surcouf\PhpArchive\Config\Icon;
use Surcouf\PhpArchive\Helper\ConverterHelper;
use Surcouf\PhpArchive\Mail\Account;

if (!defined('CORE2'))
  exit;

class ConfigObject implements IConfigurationValue {

  private $id, $parentid;
  private $name, $description, $dbValue, $value, $editable, $edited, $reqPermissionLevel;
  private $childs = array();
  private $childsByName = array();
  private $kind = EConfigurationType::TypeUnknown;

  public $dberrno = 0;
  public $dberror = '';

  function __construct($data) {
    $this->id = intval($data['config_id']);
    $this->parentid = (!is_null($data['parent_id']) ? intval($data['parent_id']) : null);
    $this->name = $data['config_name'];
    $this->description = (!is_null($data['config_description']) ? $data['config_description'] : '');
    $this->editable = ConverterHelper::to_bool($data['config_editable']);
    $this->dbValue = json_decode($data['config_value'], true);
    $this->edited = ($data['edit_time'] === null ? null : new DateTime($data['edit_time']));
    $this->reqPermissionLevel = (!is_null($data['permission_id']) ? intval($data['permission_id']) : null);

    $this->prepareValue();

  }

  public function __get(string $name) {
    if ($name == 'dberror')
      return $this->dberror;
    if ($name == 'dberrno')
      return $this->dberrno;
    if (!array_key_exists($name, $this->childsByName))
    {
      var_dump($name);
      var_dump($this);
      exit;
    }
    return $this->childsByName[$name];
  }

  public function __isset(string $name) {
    return array_key_exists($name, $this->childsByName);
  }

  public function addChild(ConfigObject &$child) : ConfigObject {
    $this->childs[$child->getId()] =& $child;
    $this->childsByName[$child->getname()] =& $this->childs[$child->getId()];
    return $this;
  }

  public function getArray() : ?array {
    if ($this->kind == EConfigurationType::TypeArray ||
        $this->kind == EConfigurationType::TypeResponseCode)
      return $this->value;
    return null;
  }

  public function getBool() : ?bool {
    if ($this->kind == EConfigurationType::TypeBoolean)
      return $this->value;
    return null;
  }

  public function getChild($n) : ?\Surcouf\PhpArchive\ConfigurationValue {
    if (array_key_exists($n, $this->childs))
      return $this->childs[$n];
    return null;
  }

  public function getChilds() : array {
    return $this->childs;
  }

  public function getDateTime() : ?DateTime {
    if ($this->kind == EConfigurationType::TypeDateTime)
      return $this->value;
    return null;
  }

  public function getDescription() : string {
    return $this->description;
  }

  public function getFloat() : ?float {
    if ($this->kind == EConfigurationType::TypeFloat)
      return $this->value;
    return null;
  }

  public function getIcon(?string $cssClass=null, ?string $customStyle=null, ?string $id=null) : string {
    if ($this->kind == EConfigurationType::TypeIcon)
      return $this->value->getIcon($cssClass, $customStyle, $id);
    return null;
  }

  public function getIconObj() : ?Icon {
    if ($this->kind == EConfigurationType::TypeIcon)
      return $this->value;
    return null;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getInt() : ?int {
    if ($this->kind == EConfigurationType::TypeInt)
      return $this->value;
    return null;
  }

  public function getLastUpdate() : ?DateTime {
    return $this->edited;
  }

  public function getMailAccount() : ?Account {
    if ($this->kind == EConfigurationType::TypeMailAccount)
      return $this->value;
    return null;
  }

  public function getName() : string {
    return $this->name;
  }

  public function getParentId() : ?int {
    return $this->parentid;
  }

  public function getResponseCode() : ?array {
    if ($this->kind == EConfigurationType::TypeResponseCode)
      return array(
        'Result' => array(
          'Success' => ConverterHelper::to_bool($this->value['success']),
          'Error' => array(
            'Code' => intval($this->value['code']),
            'Message' => $this->value['message'],
          )
        )
      );
    return null;
  }

  public function getString() : ?string {
    if ($this->kind == EConfigurationType::TypeString)
      return $this->value;
    return null;
  }

  public function getTimespan() : ?DateInterval {
    if ($this->kind == EConfigurationType::TypeTimespan)
      return $this->value;
    return null;
  }

  public function getType() : int {
    return $this->kind;
  }

  public function isEditable() : bool {
    return $this->editable;
  }

  private function prepareValue() {

    if (count($this->dbValue) == 0) {
      $this->kind = EConfigurationType::TypeGroup;
      return;
    }

    if (array_key_exists('array', $this->dbValue))
      $this->prepareArrayValue();

    if (array_key_exists('bool', $this->dbValue))
      $this->prepareBooleanValue();

    if (array_key_exists('datetime', $this->dbValue))
      $this->prepareDateTimeValue();

    if (array_key_exists('float', $this->dbValue))
      $this->prepareFloatValue();

    if (array_key_exists('icon', $this->dbValue))
      $this->prepareIconValue();

    if (array_key_exists('int', $this->dbValue))
      $this->prepareIntegerValue();

    if (array_key_exists('mail', $this->dbValue))
      $this->prepareMailAccountValue();

    if (array_key_exists('response', $this->dbValue))
      $this->prepareResponseCodeValue();

    if (array_key_exists('string', $this->dbValue))
      $this->prepareStringValue();

    if (array_key_exists('timespan', $this->dbValue))
      $this->prepareTimespanValue();

  }

  private function prepareArrayValue() {
    $this->value = $this->dbValue['array'];
    $this->kind = EConfigurationType::TypeArray;
  }

  private function prepareBooleanValue() {
    $this->value = boolval($this->dbValue['bool']);
    $this->kind = EConfigurationType::TypeBoolean;
  }

  private function prepareDateTimeValue() {
    $this->value = new DateTime($this->dbValue['datetime']);
    $this->kind = EConfigurationType::TypeDateTime;
  }

  private function prepareFloatValue() {
    $this->value = floatval($this->dbValue['float']);
    $this->kind = EConfigurationType::TypeFloat;
  }

  private function prepareIconValue() {
    $this->value = new Icon($this->dbValue['icon']);
    $this->kind = EConfigurationType::TypeIcon;
  }

  private function prepareIntegerValue() {
    $this->value = intval($this->dbValue['int']);
    $this->kind = EConfigurationType::TypeInt;
  }

  private function prepareMailAccountValue() {
    $this->value = new Account($this->dbValue['mail']);
    $this->kind = EConfigurationType::TypeMailAccount;
  }

  private function prepareResponseCodeValue() {
    $this->value = $this->dbValue['response'];
    $this->kind = EConfigurationType::TypeResponseCode;
  }

  private function prepareStringValue() {
    $this->value = $this->dbValue['string'];
    $this->kind = EConfigurationType::TypeString;
  }

  private function prepareTimespanValue() {
    $this->value = new DateInterval($this->dbValue['timespan']);
    $this->kind = EConfigurationType::TypeTimespan;
  }

  private function pushToDb(string $payload) {
    global $db, $Router;

    if ($stmt = $db->prepare('UPDATE `config` SET `config_value`=?, `user_id`=? WHERE `config_id`=?')) {

      $userid = $Controller->User()->getId();
      $stmt->bind_param('sii', $payload, $userid, $this->id);
      if (!$stmt->execute()) {
        $this->dberrno = $stmt->errno;
        $this->dberror = $stmt->error;
        return false;
      }
      if ($stmt->affected_rows == 1) {
        $stmt->close();
        return true;
      }
      $stmt->close();
      $this->dberrno = -1;
      $this->dberror = 'STMT returned 0';
      return false;

    } else {
      $this->dberrno = $db->errno;
      $this->dberror = $db->error;
      return false;
    }

  }

  public function setArray(array $newValue) : bool {
    if (!$this->set__checkPermission())
      return false;
    $jsonvalue = json_encode(array('array' => $newValue));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newValue;
      return true;
    }
    return false;
  }

  public function setBoolean(bool $newValue) : bool {
    if (!$this->set__checkPermission())
      return false;
    $jsonvalue = json_encode(array('bool' => $newValue));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newValue;
      return true;
    }
    return false;
  }

  public function setDateTime(DateTime $newValue) : bool {
    if (!$this->set__checkPermission())
      return false;
    $jsonvalue = json_encode(array('datetime' => $newValue->format(DTF_SQL)));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newValue;
      return true;
    }
    return false;
  }

  public function setFloat(float $newValue) : bool {
    if (!$this->set__checkPermission())
      return false;
    $jsonvalue = json_encode(array('float' => $newValue));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newValue;
      return true;
    }
    return false;
  }

  public function setIcon(string $newSpace, string $newIcon) : bool {
    if (!$this->set__checkPermission())
      return false;
    $newIcon = new Icon(array(
      'icon' => $newIcon,
      'space' => $newSpace,
    ));
    $jsonvalue = json_encode(array('icon' => $newIcon->getDataArray()));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newIcon;
      return true;
    }
    return false;
  }

  public function setInt(int $newValue) : bool {
    if (!$this->set__checkPermission())
      return false;
    $jsonvalue = json_encode(array('int' => $newValue));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newValue;
      return true;
    }
    return false;
  }

  public function setResponse(int $newCode, string $newMessage, bool $newResult) : bool {
    if (!$this->set__checkPermission())
      return false;
    $newResponse = array(
      'code' => $newCode,
      'message' => $newMessage,
      'success' => $newResult,
    );
    $jsonvalue = json_encode(array('response' => $newResponse));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newResponse;
      return true;
    }
    return false;
  }

  public function setString(string $newValue) : bool {
    if (!$this->set__checkPermission())
      return false;
    $jsonvalue = json_encode(array('string' => $newValue));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newValue;
      return true;
    }
    return false;
  }

  public function setTimespan(DateInterval $newValue) : bool {
    if (!$this->set__checkPermission())
      return false;
    $jsonvalue = json_encode(array('timespan' => DateTimeHelper::dateInterval2IsoFormat($newValue)));
    if ($this->pushToDb($jsonvalue)) {
      $this->value = $newValue;
      return true;
    }
    return false;
  }

  private function set__checkPermission() : bool {
    global $Acl, $Router;
    if (!$this->editable)
      return false;
    if ($Controller->User()->isGuest())
      return false;
    if (!is_null($this->reqPermissionLevel)) {
      if (!$Controller->User()->may($Acl->getPermission($this->reqPermissionLevel)))
        return false;
    }
    return true;
  }

}
