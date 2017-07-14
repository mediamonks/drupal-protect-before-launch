<?php

namespace Drupal\Tests\protect_before_launch\Functional;

use Drupal\protect_before_launch\Service\Configuration;
use Drupal\simpletest\WebTestBase;

/**
 * Class AuthenticateTest.
 *
 * @group tests
 * @package Drupal\Tests\protect_before_launch\Functional
 */
class AuthenticateTest extends WebTestBase {

  protected static $modules = ['protect_before_launch'];

  /**
   * Test that protection is disabled.
   */
  public function testDisabled() {

    $configFactory = \Drupal::service('config.factory');
    $config = $configFactory->getEditable(Configuration::CONFIG_KEY);
    $config->set('protect', Configuration::CONFIG_DISABLED);
    $config->save();

    $this->drupalGet('');

    $authenticateHeader = FALSE;
    $unauthorizedHeader = FALSE;

    foreach ($this->headers as $header) {
      if (trim($header) == 'HTTP/1.1 401 Unauthorized') {
        $authenticateHeader = TRUE;
      }
      if (trim($header) == 'WWW-Authenticate: Basic realm="Access denied"') {
        $unauthorizedHeader = TRUE;
      }
    }
    $this->assertFalse($authenticateHeader, 'Search for Unauthorized header to be absent');
    $this->assertFalse($unauthorizedHeader, 'Search for WWW-Authenticate header to be absent');
  }

  /**
   * Test protection enabled.
   */
  public function testEnabled() {

    $configFactory = \Drupal::service('config.factory');
    $config = $configFactory->getEditable(Configuration::CONFIG_KEY);
    $config->set('protect', Configuration::CONFIG_ENABLED);
    $config->save();

    $this->drupalGet('');

    $authenticateHeader = FALSE;
    $unauthorizedHeader = FALSE;

    foreach ($this->headers as $header) {
      if (trim($header) == 'HTTP/1.1 401 Unauthorized') {
        $authenticateHeader = TRUE;
      }
      if (trim($header) == 'WWW-Authenticate: Basic realm="Protected Site"') {
        $unauthorizedHeader = TRUE;
      }
    }
    $this->assertTrue($authenticateHeader, 'Search for Unauthorized header to be present');
    $this->assertTrue($unauthorizedHeader, 'Search for WWW-Authenticate header to be present');
    $this->assertTrue(($this->content == 'Not allowed' ? TRUE : FALSE), 'Check content');
  }

  /**
   * Test protection enabled.
   */
  public function testAuthentication() {

    $configFactory = \Drupal::service('config.factory');
    $config = $configFactory->getEditable(Configuration::CONFIG_KEY);
    $config->set('protect', Configuration::CONFIG_ENABLED);
    $config->save();

    $this->drupalGet('');
    $authenticateHeader = FALSE;
    $unauthorizedHeader = FALSE;

    foreach ($this->headers as $header) {
      if (trim($header) == 'HTTP/1.1 401 Unauthorized') {
        $authenticateHeader = TRUE;
      }
      if (trim($header) == 'WWW-Authenticate: Basic realm="Protected Site"') {
        $unauthorizedHeader = TRUE;
      }
    }
    $this->assertTrue($authenticateHeader, 'Unauthorized: Search for Unauthorized header to be present');
    $this->assertTrue($unauthorizedHeader, 'Unauthorized: Search for WWW-Authenticate header to be present');
    $this->assertTrue(($this->content == 'Not allowed' ? TRUE : FALSE), 'Unauthorized: Check content');

    $this->drupalGet('', [], ['Authorization:Basic ' . base64_encode('username:password')]);
    $authenticateHeader = FALSE;
    $unauthorizedHeader = FALSE;

    foreach ($this->headers as $header) {
      if (trim($header) == 'HTTP/1.1 401 Unauthorized') {
        $authenticateHeader = TRUE;
      }
      if (trim($header) == 'WWW-Authenticate: Basic realm="Protected Site"') {
        $unauthorizedHeader = TRUE;
      }
    }
    $this->assertFalse($authenticateHeader, 'Unauthorized: Search for Unauthorized header to be present');
    $this->assertFalse($unauthorizedHeader, 'Unauthorized: Search for WWW-Authenticate header to be present');
    $this->assertFalse(($this->content == 'Not allowed' ? TRUE : FALSE), 'Unauthorized: Check content');

  }

  /**
   * Test Customizing values.
   */
  public function testChangeContent() {

    $time = time();
    $configFactory = \Drupal::service('config.factory');
    $config = $configFactory->getEditable(Configuration::CONFIG_KEY);
    $config->set('protect', Configuration::CONFIG_ENABLED);
    $config->set('realm', $time);
    $config->set('content', $time);
    $config->save();

    $this->drupalGet('');
    $unauthorizedHeader = FALSE;

    foreach ($this->headers as $header) {
      if (trim($header) == 'WWW-Authenticate: Basic realm="' . $time . '"') {
        $unauthorizedHeader = TRUE;
      }
    }
    $this->assertTrue($unauthorizedHeader, 'Search for custom WWW-Authenticate header to be present');
    $this->assertTrue(($this->content == $time ? TRUE : FALSE), 'Check custom content');
  }

}
