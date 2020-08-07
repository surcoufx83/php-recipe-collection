<?php

namespace Surcouf\PhpArchive\Config;

use Surcouf\PhpArchive\Config\EConfigurationType;
use Surcouf\PhpArchive\Config\ConfigObject;

if (!defined('CORE2'))
  exit;

final class Configuration {

  private $childs = array();
  private $childsByName = array();
  private $responseCodes = array();

  public function __get(string $name) {
    return $this->childsByName[$name];
  }

  public function __isset(string $name) {
    return array_key_exists($name, $this->childsByName);
  }

  public function addChild(array $record) : Configuration {
    $child = new ConfigObject($record);
    $this->childs[$child->getId()] = $child;
    if (!array_key_exists($child->getname(), $this->childsByName))
      $this->childsByName[$child->getname()] =& $this->childs[$child->getId()];
    if (!is_null($child->getParentId()) && array_key_exists($child->getParentId(), $this->childs))
      $this->childs[$child->getParentId()]->addChild($this->childs[$child->getId()]);
    if ($child->getType() == EConfigurationType::TypeResponseCode)
      $this->responseCodes[$child->getArray()['code']] =& $this->childs[$child->getId()];
    return $this;
  }

  public function getResponse(int $responseCode) : ConfigObject {
    return $this->responseCodes[$responseCode];
  }

  public function getResponseArray(int $responseCode) : ?Array {
    return $this->responseCodes[$responseCode]->getResponseCode();
  }

}
