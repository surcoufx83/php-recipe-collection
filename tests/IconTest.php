<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Config\Icon;

require_once realpath(__DIR__.'/../private/entities/Config/IconInterface.php');
require_once realpath(__DIR__.'/../private/entities/Config/Icon.php');

/**
 * @covers Icon::<public>
 */
class IconTest extends TestCase
{

  /**
   * Icon $faicon
   */
  protected $faicon;

  /**
   * Icon $icicon
   */
  protected $icicon;

  protected function setUp() : void {
    parent::setUp();
    $this->faicon = new Icon(['space' => 'fas', 'icon' => 'foo']);
    $this->icicon = new Icon(['space' => 'ico', 'icon' => 'foo']);
  }

  /**
   * @covers Config::getIcon()
   */
  public function testGetIcon() {
    $this->assertEquals('<i class="fas fa-foo"></i>', $this->faicon->getIcon());
    $this->assertEquals('<i class="fas fa-foo"></i>', $this->faicon->getIcon(null));
    $this->assertEquals('<i class="fas fa-foo"></i>', $this->faicon->getIcon(null, null));
    $this->assertEquals('<i class="fas fa-foo"></i>', $this->faicon->getIcon(null, null, null));
    $this->assertEquals('<i class="bar fas fa-foo"></i>', $this->faicon->getIcon('bar'));
    $this->assertEquals('<i class="bar fas fa-foo"></i>', $this->faicon->getIcon('bar', null));
    $this->assertEquals('<i class="bar fas fa-foo"></i>', $this->faicon->getIcon('bar', null, null));
    $this->assertEquals('<i class="bar fas fa-foo" style="foobar"></i>', $this->faicon->getIcon('bar', 'foobar'));
    $this->assertEquals('<i class="bar fas fa-foo" style="foobar"></i>', $this->faicon->getIcon('bar', 'foobar', null));
    $this->assertEquals('<i class="bar fas fa-foo" style="foobar" id="foo-id"></i>', $this->faicon->getIcon('bar', 'foobar', 'foo-id'));
    $this->assertEquals('<i class="icofont-foo"></i>', $this->icicon->getIcon());
    $this->assertEquals('<i class="icofont-foo"></i>', $this->icicon->getIcon(null));
    $this->assertEquals('<i class="icofont-foo"></i>', $this->icicon->getIcon(null, null));
    $this->assertEquals('<i class="icofont-foo"></i>', $this->icicon->getIcon(null, null, null));
    $this->assertEquals('<i class="bar icofont-foo"></i>', $this->icicon->getIcon('bar'));
    $this->assertEquals('<i class="bar icofont-foo"></i>', $this->icicon->getIcon('bar', null));
    $this->assertEquals('<i class="bar icofont-foo"></i>', $this->icicon->getIcon('bar', null, null));
    $this->assertEquals('<i class="bar icofont-foo" style="foobar"></i>', $this->icicon->getIcon('bar', 'foobar'));
    $this->assertEquals('<i class="bar icofont-foo" style="foobar"></i>', $this->icicon->getIcon('bar', 'foobar', null));
    $this->assertEquals('<i class="bar icofont-foo" style="foobar" id="foo-id"></i>', $this->icicon->getIcon('bar', 'foobar', 'foo-id'));
  }

  /**
   * @covers Config::getDataArray()
   */
  public function testGetDataArray() {
    $this->assertEquals(['space' => 'fas', 'icon' => 'foo'], $this->faicon->getDataArray());
    $this->assertEquals(['space' => 'ico', 'icon' => 'foo'], $this->icicon->getDataArray());
  }

}
