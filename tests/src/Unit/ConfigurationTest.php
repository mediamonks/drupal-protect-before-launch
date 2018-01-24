<?php

namespace Drupal\Tests\protect_before_launch\Unit;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
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

  public function testSet()
  {
    $editableConfig = $this->createMock(Config::class);
    $editableConfig->expects($this->at(0), $this->once())->method('set')->with('username', 'my_username');
    $editableConfig->expects($this->at(2), $this->once())->method('set')->with('password', $this->anything());
    $editableConfig->expects($this->at(4), $this->once())->method('set')->with('realm', 'my_realm');
    $editableConfig->expects($this->at(6), $this->once())->method('set')->with('protect', Configuration::CONFIG_ENABLED);
    $editableConfig->expects($this->at(8), $this->once())->method('set')->with('content', 'my_content');
    $editableConfig->expects($this->at(10), $this->once())->method('set')->with('environment_key', 'my_environment_key');
    $editableConfig->expects($this->at(12), $this->once())->method('set')->with('environment_value', 'my_environment_value');
    $editableConfig->expects($this->at(14), $this->once())->method('set')->with('exclude_paths', 'my_exclude_paths');

    $immutableConfig = $this->createMock(ImmutableConfig::class);

    $configuration = $this->createMock(ConfigFactoryInterface::class);
    $configuration->method('getEditable')->willReturn($editableConfig);
    $configuration->method('get')->willReturn($immutableConfig);

    $configuration = new Configuration($configuration);
    $configuration->setUsername('my_username');
    $configuration->setPassword('my_password');
    $configuration->setRealm('my_realm');
    $configuration->setProtect(Configuration::CONFIG_ENABLED);
    $configuration->setContent('my_content');
    $configuration->setEnvironmentKey('my_environment_key');
    $configuration->setEnvironmentValue('my_environment_value');
    $configuration->setExcludePaths('my_exclude_paths');
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
