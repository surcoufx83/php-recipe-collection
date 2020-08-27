<?php

namespace Surcouf\Cookbook\Config;

if (!defined('CORE2'))
  exit;

class Icon implements IIcon {

  private $space, $icon;

  function __construct($data) {
    $this->icon = $data['icon'];
    $this->space = $data['space'];
  }

  public function getIcon(?string $cssClass=null, ?string $customStyle=null, ?string $id=null) : string {
    if ($this->space == 'ico')
      return $this->getCustomIcofontIcon($cssClass, $customStyle, $id);
    return $this->getCustomFontAwesomeIcon($cssClass, $customStyle, $id);
  }

  private function getCustomFontAwesomeIcon(string $css = null, string $style = null, string $id = null) : string {
    return '<i class="'.(!is_null($css) ? $css.' ' : '').$this->space.' fa-'.$this->icon.'"'.(!is_null($style) ? ' style="'.$style.'"' : '').(!is_null($id) ? ' id="'.$id.'"' : '').'></i>';
  }

  private function getCustomIcofontIcon(string $css = null, string $style = null, string $id = null) : string {
    return '<i class="'.(!is_null($css) ? $css.' ' : '').'icofont-'.$this->icon.'"'.(!is_null($style) ? ' style="'.$style.'"' : '').(!is_null($id) ? ' id="'.$id.'"' : '').'></i>';
  }

  public function getDataArray() : array {
    return array(
      'space' => $this->space,
      'icon' => $this->icon,
    );
  }

  private function getName() : string {
    return $this->space.' '.' fa-'.$this->icon;
  }

  private function getSpace() : string {
    return $this->space;
  }

}
