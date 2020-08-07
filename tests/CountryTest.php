<?php

use PHPUnit\Framework\TestCase;
use Surcouf\PhpArchive\Helper\ConverterHelper;
use Surcouf\PhpArchive\Country;
use Surcouf\PhpArchive\ICountry;

require_once realpath(__DIR__.'/../private/entities/Helper/IConverterHelper.php');
require_once realpath(__DIR__.'/../private/entities/Helper/ConverterHelper.php');
require_once realpath(__DIR__.'/../private/entities/ICountry.php');
require_once realpath(__DIR__.'/../private/entities/Country.php');

/**
 * @covers Country::<public>
 */
class CountryTest extends TestCase
{
  /** @var Country */
  private $Country;

  protected function setUp() : void {
    parent::setUp();
    $this->Country = new Country(array(
      'country_id' => 1,
      'country_code' => 'DE',
      'country_name' => 'Germany',
      'country_name_de' => 'Deutschland',
      'country_envelope_show' => 0,
      'country_envelope_name' => 'GERMANY',
      'country_zip_pattern' => '\\d{5}',
    ));
  }

  /**
   * @covers Country::getCode()
   */
  public function testCountryGetCode() {
    $this->assertEquals('DE', $this->Country->getCode());
  }

  /**
   * @covers Country::getEnvelopeName()
   */
  public function testCountryGetEnvelopeName() {
    $this->assertEquals('GERMANY', $this->Country->getEnvelopeName());
  }

  /**
   * @covers Country::getId()
   */
  public function testCountryGetId() {
    $this->assertEquals(1, $this->Country->getId());
  }

  /**
   * @covers Country::getName()
   */
  public function testCountryGetName() {
    $this->assertEquals('Deutschland', $this->Country->getName());
  }

  /**
   * @covers Country::getName()
   */
  public function testCountryGetNameEn() {
    $this->assertEquals('Germany', $this->Country->getNameEn());
  }

  /**
   * @covers Country::getZipPattern()
   */
  public function testCountryGetZipPattern() {
    $this->assertEquals('\\d{5}', $this->Country->getZipPattern());
  }

  /**
   * @covers Country::showOnEnvelope()
   */
  public function testCountryShowOnEnvelope() {
    $this->assertFalse($this->Country->showOnEnvelope());
  }

  /**
   * @covers Country::validateZip()
   */
  public function testCountryValidateZip() {
    $this->assertTrue($this->Country->validateZip('12345'));
    $this->assertFalse($this->Country->validateZip(''));
    $this->assertFalse($this->Country->validateZip('WC2N 5DU'));
  }

}
