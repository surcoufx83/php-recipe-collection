<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use Surcouf\Cookbook\Controller\LinkProvider;

require_once realpath(__DIR__.'/../private/entities/Controller/LinkProvider.php');

/**
 * @covers LinkProvider
 */
class LinkProviderTest extends TestCase
{

  /**
   * @var $provider LinkProvider
   */
  private $provider;

  protected function setUp() : void {
    parent::setUp();
    $this->provider = new LinkProvider();
  }

  /**
   * @covers LinkProvider::__call
   * @dataProvider methodsProvider
   */
  public function testMethods($route, $expected, $args) {
    $this->assertSame($expected, $this->provider->$route($args));
  }

  /**
   * @covers LinkProvider::__get
   * @dataProvider goodPropertiesProvider
   */
  public function testProperties($route, $expected) {
    $this->assertSame($expected, $this->provider->$route);
  }

  /**
   * @covers LinkProvider::__get
   * @dataProvider badPropertiesProvider
   */
  public function testPropertiesFailed($route) {
    $this->assertNull($this->provider->$route);
  }

  public function methodsProvider() {
    return [
      ['admin_ajax_testEntity', '/admin/test/entity', []],
      ['admin_cronjobs', '/admin/cronjobs', []],
      ['admin_logs', '/admin/logs', []],
      ['admin_main', '/admin', []],
      ['admin_new-user', '/admin/new-user', []],
      ['admin_new-user-post', '/admin/new-user', []],
      ['admin_oauth_auth', '', []],
      ['admin_oauth_redirect', 'https://foo.bar/oauth2/callback', []],
      ['admin_oauth_token', '', []],
      ['admin_oauth_user', '', []],
      ['admin_settings', '/admin/settings', []],
      ['admin_recipe_remove', '/admin/recipe/remove/1/foo', [1, 'foo']],
      ['admin_recipe_unpublish', '/admin/recipe/unpublish/1/foo', [1, 'foo']],
      ['admin_user', '/admin/user/1/foo', [1, 'foo']],
      ['admin_user', '/admin/user/2/foobar', [2, 'foobar']],
      ['admin_users', '/admin/users', []],
      ['maintenance', '/maintenance', []],
      ['private_activation', 'https://foo.bar/activate/foo', ['foo']],
      ['private_activation', 'https://foo.bar/activate/bar', ['bar']],
      ['private_activatePassword', '/activate-account/foo', ['foo']],
      ['private_activatePassword', '/activate-account/bar', ['bar']],
      ['private_avatar', '/pictures/avatars/foo', ['foo']],
      ['private_avatar', '/pictures/avatars/bar', ['bar']],
      ['private_books', '/books', []],
      ['private_login', '/login', []],
      ['private_login-oauth2', '/oauth2/login', []],
      ['private_logout', '/logout', []],
      ['private_home', '/', []],
      ['private_random', '/random', []],
      ['private_recipes', '/myrecipes', []],
      ['private_search', '/search', []],
      ['private_self-register', '/self-register', []],
      ['private_settings', '/settings', []],
      ['recipe_new', '/recipe/new', []],
      ['recipe_picture_link', '/pictures/cbimages/foo', ['foo']],
      ['recipe_picture_link', '/pictures/cbimages/foobar', ['foobar']],
      ['recipe_postnew', '/recipe/new', []],
      ['recipe_publish', '/recipe/publish/1/foo', [1, 'foo']],
      ['recipe_publish', '/recipe/publish/2/foobar', [2, 'foobar']],
      ['recipe_show', '/1/foo', [1, 'foo']],
      ['recipe_show', '/2/foobar', [2, 'foobar']],
      ['recipe_unpublish', '/recipe/unpublish/1/foo', [1, 'foo']],
      ['recipe_unpublish', '/recipe/unpublish/2/foobar', [2, 'foobar']],
      ['tag_show', '/tag/1/foo', [1, 'foo']],
      ['tag_show', '/tag/2/foobar', [2, 'foobar']],
    ];
  }

  public function goodPropertiesProvider() {
    return [
      ['admin_ajax_testEntity', '/admin/test/entity'],
      ['admin_cronjobs', '/admin/cronjobs'],
      ['admin_logs', '/admin/logs'],
      ['admin_main', '/admin'],
      ['admin_new-user', '/admin/new-user'],
      ['admin_new-user-post', '/admin/new-user'],
      ['admin_oauth_auth', ''],
      ['admin_oauth_redirect', 'https://foo.bar/oauth2/callback'],
      ['admin_oauth_token', ''],
      ['admin_oauth_user', ''],
      ['admin_settings', '/admin/settings'],
      ['admin_users', '/admin/users'],
      ['maintenance', '/maintenance'],
      ['private_books', '/books'],
      ['private_login', '/login'],
      ['private_login-oauth2', '/oauth2/login'],
      ['private_logout', '/logout'],
      ['private_home', '/'],
      ['private_random', '/random'],
      ['private_recipes', '/myrecipes'],
      ['private_search', '/search'],
      ['private_self-register', '/self-register'],
      ['private_settings', '/settings'],
      ['recipe_new', '/recipe/new'],
      ['recipe_postnew', '/recipe/new'],
    ];
  }

  public function badPropertiesProvider() {
    return [
      ['admin_recipe_remove'],
      ['admin_recipe_unpublish'],
      ['admin_user'],
      ['private_activation'],
      ['private_activatePassword'],
      ['private_avatar'],
      ['recipe_picture_link'],
      ['recipe_publish'],
      ['recipe_show'],
      ['recipe_sendVoting'],
      ['recipe_unpublish'],
      ['tag_show'],
      ['user_recipes'],
    ];
  }

}
