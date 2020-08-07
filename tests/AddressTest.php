<?php

use PHPUnit\Framework\TestCase;
use Surcouf\PhpArchive\Address;
use Surcouf\PhpArchive\IAddress;
use Surcouf\PhpArchive\Country;
use Surcouf\PhpArchive\ICountry;
use Surcouf\PhpArchive\IController;

require_once realpath(__DIR__.'/../private/entities/IAddress.php');
require_once realpath(__DIR__.'/../private/entities/Address.php');
require_once realpath(__DIR__.'/../private/entities/IController.php');
require_once realpath(__DIR__.'/../private/entities/ICountry.php');
require_once realpath(__DIR__.'/../private/entities/Country.php');

/**
 * @covers Address::<public>
 */
class AddressTest extends TestCase
{
  /** @var Address */
  private $Address;

  /** @var Controller */
  private $Controller;

  protected function setUp() : void {
    global $Controller;
    parent::setUp();
    $this->Controller = $this->getMockBuilder(IController::class)
      ->disableOriginalConstructor()
      ->getMock();
    $Controller = $this->Controller;
    $this->Address = new Address(array(
      'address_id' => 1,
      'country_id' => 1,
      'address_title' => 'foo',
      'address_line1' => 'foo bar',
      'address_line2' => 'foo Rd. 1',
      'address_line3' => '',
      'address_line4' => '',
      'address_zip' => '12345',
      'address_city' => 'foo town',
    ));
  }

  /**
   * @covers Address::getCity()
   */
  public function testAddressGetCity() {
    $this->assertEquals('foo town', $this->Address->getCity());
  }

  /**
   * @covers Address::getCountry()
   */
  public function testAddressGetCountry() {
    $stub = $this->createMock(Country::class);
    $this->Controller
      ->expects($this->once())
      ->method('getCountry')
      ->willReturn($stub);
    $this->assertInstanceOf(Country::class, $this->Address->getCountry());
  }

  /**
   * @covers Address::getCountryId()
   */
  public function testAddressGetCountryId() {
    $this->assertEquals(1, $this->Address->getCountryId());
  }

  /**
   * @covers Address::getId()
   */
  public function testAddressGetId() {
    $this->assertEquals(1, $this->Address->getId());
  }

  /**
   * @covers Address::getLine1()
   */
  public function testAddressGetLine1() {
    $this->assertEquals('foo bar', $this->Address->getLine1());
  }

  /**
   * @covers Address::getLine2()
   */
  public function testAddressGetLine2() {
    $this->assertEquals('foo Rd. 1', $this->Address->getLine2());
  }

  /**
   * @covers Address::getLine3()
   */
  public function testAddressGetLine3() {
    $this->assertEquals('', $this->Address->getLine3());
  }

  /**
   * @covers Address::getLine4()
   */
  public function testAddressGetLine4() {
    $this->assertEquals('', $this->Address->getLine4());
  }

  /**
   * @covers Address::getName()
   */
  public function testAddressGetName() {
    $this->assertEquals('foo', $this->Address->getName());
  }

  /**
   * @covers Address::getZip()
   */
  public function testAddressGetZip() {
    $this->assertEquals('12345', $this->Address->getZip());
  }

}
