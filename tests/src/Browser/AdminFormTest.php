<?php

namespace Drupal\Tests\protect_before_launch\Browser;

use Drupal\protect_before_launch\Configuration;
use Drupal\Tests\BrowserTestBase;

/**
 * Class AdminFormTest.
 *
 * @group tests
 * @package Drupal\Tests\protect_before_launch\Browser
 */
class AdminFormTest extends BrowserTestBase {

  protected $defaultTheme = 'stark';

  protected static $modules = ['protect_before_launch'];

  /**
   * Setup admin user.
   */
  protected function setUp() {
    parent::setUp();

    $admin = $this->drupalCreateUser(['administer modules', 'administer protect before launch'], 'administrator', TRUE);
    $this->drupalLogin($admin);
    $this->drupalGet('admin/config/protect_before_launch/settings');

  }

  /**
   * Test admin is able to view the settings form.
   */
  public function testViewForm() {

    $output = $this->getSession()->getPage()->getContent();
    $this->assertSession()->pageTextContains('Status', $output);
    $this->assertSession()->pageTextContains('Username', $output);
    $this->assertSession()->pageTextContains('Password', $output);
    $this->assertSession()->pageTextContains('Confirm password', $output);
    $this->assertSession()->pageTextContains('Authentication', $output);
    $this->assertSession()->pageTextContains('Realm', $output);
    $this->assertSession()->pageTextContains('Exclude Paths', $output);
    $this->assertSession()->pageTextContains('Environment Key', $output);
    $this->assertSession()->pageTextContains('Environment Value', $output);

    $this->assertSession()->fieldValueEquals('protect', Configuration::PROTECT_DISABLED);
    $this->assertSession()->fieldValueEquals('username', 'username');
    $this->assertSession()->fieldValueEquals('content', 'Access Denied');
    $this->assertSession()->fieldValueEquals('realm', 'Protected Site');
    $this->assertSession()->fieldValueEquals('authentication_type', Configuration::AUTH_SIMPLE);
    $this->assertSession()->fieldValueEquals('exclude_paths', '');
    $this->assertSession()->fieldValueEquals('environment_key', 'AH_NON_PRODUCTION');
    $this->assertSession()->fieldValueEquals('environment_value', '');
  }

  /**
   * Test admin is able to update the settings form.
   */
  public function testUpdateForm() {
    $this->submitForm([
      'username' => 'my_username',
      'realm' => 'My Realm',
      'content' => 'My Protected Content',
      'environment_key' => 'MY_KEY',
      'environment_value' => 'MY_VALUE'
    ], 'Save Configuration');

    $output = $this->drupalGet('admin/config/protect_before_launch/settings');
    $this->assertSession()->pageTextContains('Username', $output);
    $this->assertSession()->fieldValueEquals('protect', Configuration::PROTECT_DISABLED);
    $this->assertSession()->fieldValueEquals('username', 'my_username');
    $this->assertSession()->fieldValueEquals('content', 'My Protected Content');
    $this->assertSession()->fieldValueEquals('realm', 'My Realm');
    $this->assertSession()->fieldValueEquals('authentication_type', Configuration::AUTH_SIMPLE);
    $this->assertSession()->fieldValueEquals('exclude_paths', '');
    $this->assertSession()->fieldValueEquals('environment_key', 'MY_KEY');
    $this->assertSession()->fieldValueEquals('environment_value', 'MY_VALUE');
  }

  /**
   * Test admin is able to enable the form.
   */
  public function testEnable() {
    $this->submitForm([
      'protect' => Configuration::PROTECT_ENABLED,
    ], 'Save Configuration');

    $output = $this->drupalGet('admin/config/protect_before_launch/settings');
    $this->assertEquals('Access Denied', $output);

    $output = $this->drupalGet('admin/config/protect_before_launch/settings', [], [
      'PHP_AUTH_USER' => 'username',
      'PHP_AUTH_PW' => 'password'
    ]);
    $this->assertSession()->pageTextContains('Username', $output);
  }

}
