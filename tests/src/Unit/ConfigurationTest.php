<?php

namespace Drupal\Tests\protect_before_launch\Unit;

use Drupal\protect_before_launch\Configuration;

/**
 * Class ConfigurationTest
 *
 * @group tests
 * @package Drupal\Tests\protect_before_launch\Unit
 */
class ConfigurationTest extends UnitTestCase {

  public function testValidateCredentialsEmpty()
  {
    $configuration = $this->getConfiguration();
    $this->assertFalse($configuration->validateCredentials('foo', 'bar'));
    $this->assertFalse($configuration->validateCredentials('', ''));
    $this->assertFalse($configuration->validateCredentials(null, null));
  }

  public function testValidateCredentialsValid()
  {
    $configuration = $this->getConfiguration([
      'username' => 'foo',
      'password' => password_hash('bar', Configuration::CONFIG_HASH)
    ]);
    $this->assertTrue($configuration->validateCredentials('foo', 'bar'));
    $this->assertFalse($configuration->validateCredentials('foo', 'ber'));
    $this->assertFalse($configuration->validateCredentials('', ''));
    $this->assertFalse($configuration->validateCredentials(null, null));
  }

  /**
   * @param array $config
   *
   * @return \Drupal\protect_before_launch\Configuration
   */
  private function getConfiguration(array $config = [])
  {
    return new Configuration($this->getConfigFactoryStub([
      Configuration::CONFIG_KEY => $config
    ]));
  }
}
