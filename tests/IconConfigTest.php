<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\Config\IconConfigInterface;
use Surcouf\Cookbook\Config\IconInterface;
use Surcouf\Cookbook\Config\IconConfig;

require_once realpath(__DIR__.'/../private/entities/Config/IconInterface.php');
require_once realpath(__DIR__.'/../private/entities/Config/Icon.php');
require_once realpath(__DIR__.'/../private/entities/Config/IconConfigInterface.php');
require_once realpath(__DIR__.'/../private/entities/Config/IconConfig.php');

/**
 * @covers IconConfig::<public>
 */
class IconConfigTest extends TestCase
{

  /**
   * IconConfig $cfg
   */
  protected $cfg;

  protected function setUp() : void {
    parent::setUp();
    $this->cfg = new IconConfig();
  }

  /**
   * @covers IconConfig::__call()
   */
  public function test__call() {
    $this->assertIsString($this->cfg->Dummy());
    $this->assertEquals('<i class="fas fa-question"></i>', $this->cfg->Dummy());
    $this->assertEquals('<i class="foo fas fa-question"></i>', $this->cfg->Dummy('foo'));
    $this->assertEquals('<i class="foo fas fa-question" style="bar"></i>', $this->cfg->Dummy('foo', 'bar'));
    $this->assertEquals('<i class="foo fas fa-question" style="bar" id="fooid"></i>', $this->cfg->Dummy('foo', 'bar', 'fooid'));
  }

  /**
   * @covers IconConfig::__get()
   */
  public function test__get() {
    $this->assertInstanceOf(IconInterface::class, $this->cfg->Dummy);
  }

}
