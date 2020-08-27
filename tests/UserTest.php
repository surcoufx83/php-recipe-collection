<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Surcouf\Cookbook\IController;
use Surcouf\Cookbook\Helper\HashHelper;
use Surcouf\Cookbook\IUser;
use Surcouf\Cookbook\User;
use Surcouf\Cookbook\Database\EQueryType;
use Surcouf\Cookbook\Database\QueryBuilder;

require_once realpath(__DIR__.'/../private/entities/Database/EQueryType.php');
require_once realpath(__DIR__.'/../private/entities/Database/QueryBuilder.php');
require_once realpath(__DIR__.'/../private/entities/IController.php');
require_once realpath(__DIR__.'/../private/entities/IDbObject.php');
require_once realpath(__DIR__.'/../private/entities/IHashable.php');
require_once realpath(__DIR__.'/../private/entities/Helper/IHashHelper.php');
require_once realpath(__DIR__.'/../private/entities/Helper/HashHelper.php');
require_once realpath(__DIR__.'/../private/entities/IUser.php');
require_once realpath(__DIR__.'/../private/entities/User.php');
require_once realpath(__DIR__.'/../private/entities/User/Session.php');

/**
 * @covers User::<public>
 */
class UserTest extends TestCase
{
  /** @var User */
  private $User;

  /** @var IController|MockObject */
  private $Controller;

  protected function setUp() : void {
    parent::setUp();
    $this->Controller = $this->getMockBuilder(IController::class)
      ->disableOriginalConstructor()
      ->getMock();
    $this->User = new User(array(
      'user_id' => 0,
      'user_name' => 'foo',
      'user_hash' => null,
      'user_firstname' => 'Jane',
      'user_lastname' => 'Doe',
      'user_fullname' => 'Jane Doe',
      'user_password' => '$argon2i$v=19$m=65536,t=4,p=12$dUR2OTdVSmZNZll1SVg4Vw$bT1DChDSuhpYhuOqVg6jUOZuvCpQwe9xmdfa46rn34o',
      'user_email' => 'j.doe@foo.bar',
      'user_email_validation' => null,
      'user_email_validated' => null,
      'user_last_activity' => '2020-01-01 00:00:00',
      'user_avatar' => null,
    ));
    $this->Session = array(
      'login_id' => 1,
      'user_id' => 0,
      'login_type' => 1,
      'login_time' => '2020-01-01 00:00:00',
      'login_keep' => 1,
      'login_token' => '$argon2i$v=19$m=65536,t=4,p=12$RGVLV0ZBQmRIRWV4ZHQwUw$65rjgbQUV55HAntcJY3l5ElzM03fgZrjxMHXNC2HBlc',
      'login_password' => '$argon2i$v=19$m=65536,t=4,p=12$ZVRQMkNCWnJtWEhvZS9DVg$Z5w5ct78z0YDz97z/7tbw7bwiE/V+VgHZJxeTk/mMS4',
    );
  }

  /**
   * @covers User::calculateHash
   */
  public function testCalculateHash() {
    $this->markTestSkipped(
      'Waiting for Config redesign'
    );
  }

  /**
   * @covers User::createNewSession
   * @covers User::getSession
   */
  public function testCreateNewSessionAndGetSession() {
    $this->markTestSkipped(
      'Travis-CI does not include ARGON2I support.'
    );
    /*$result = $this->createMock(\mysqli_result::class);
    $this->Controller->expects($this->once())->method('setSessionCookies')->willReturn(true);
    $this->Controller->expects($this->once())->method('insert')->willReturn(true);
    $this->assertTrue($this->User->createNewSession(true));
    $this->assertInstanceOf(User\Session::class, $this->User->getSession());*/
  }

  /**
   * @covers User::getFirstname
   */
  public function testUserGetFirstname() {
    $this->assertEquals('Jane', $this->User->getFirstname());
  }

  /**
   * @covers User::getId
   */
  public function testUserGetId() {
    $this->assertEquals(0, $this->User->getId());
  }

  /**
   * @covers User::getInitials
   */
  public function testUserGetInitials() {
    $this->assertEquals('JD', $this->User->getInitials());
  }

  /**
   * @covers User::getLastname
   */
  public function testUserGetLastname() {
    $this->assertEquals('Doe', $this->User->getLastname());
  }

  /**
   * @covers User::getLastActivityTime
   */
  public function testUserGetLastActivityTime() {
    $this->assertInstanceOf(\DateTime::class, $this->User->getLastActivityTime());
    $test = new \DateTime('2020-01-01 00:00:00');
    $this->assertEquals($test, $this->User->getLastActivityTime());
  }

  /**
   * @covers User::getMail
   */
  public function testUserGetMail() {
    $this->assertEquals('j.doe@foo.bar', $this->User->getMail());
  }

  /**
   * @covers User::getName
   */
  public function testUserGetName() {
    $this->assertEquals('Jane Doe', $this->User->getName());
  }

  /**
   * @covers User::getPassword
   */
  public function testUserGetPassword() {
    $this->expectException(Error::class);
    $this->User->getPassword();
  }

  /**
   * @covers User::getUsername
   */
  public function testUserGetUsername() {
    $this->assertEquals('foo', $this->User->getUsername());
  }

  /**
   * @covers User::verify
   */
  public function testUserVerify() {
    $this->markTestSkipped(
      'Travis-CI does not include ARGON2I support.'
    );
    $this->Controller->expects($this->atMost(1))->method('update')->willReturn(true);
    $this->assertTrue($this->User->verify('foobar'));
  }

  /**
   * @covers User::verify
   */
  public function testUserVerifyWrongPassword() {
    $this->markTestSkipped(
      'Travis-CI does not include ARGON2I support.'
    );
    $this->assertFalse($this->User->verify('barfoo'));
  }

  /**
   * @covers User::verifySession
   * @covers User::getSession
   */
  public function testUserVerifySessionAndGetSession() {
    $this->markTestSkipped(
      'Travis-CI does not include ARGON2I support.'
    );
    $result = $this->createMock(\mysqli_result::class);
    $result->expects($this->exactly(1))->method('fetch_assoc')->willReturn($this->Session);
    $this->Controller->expects($this->exactly(1))->method('select')->willReturn($result);
    $this->Controller->expects($this->exactly(1))->method('update')->willReturn(true);
    $this->assertTrue($this->User->verifySession('token1', 'token2'));
    $this->assertInstanceOf(User\Session::class, $this->User->getSession());
  }

}
