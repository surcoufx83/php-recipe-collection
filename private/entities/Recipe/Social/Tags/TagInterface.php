<?php

namespace Surcouf\Cookbook\Recipe\Social\Tags;

if (!defined('CORE2'))
  exit;

interface TagInterface {

  public function getId() : int;
  public function getName() : string;

}
