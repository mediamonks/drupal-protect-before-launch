<?php

namespace Drupal\Tests\protect_before_launch\Unit\StackMiddleware;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Password\PasswordInterface;
use Drupal\protect_before_launch\Configuration;
use Drupal\protect_before_launch\StackMiddleware\ProtectHttpKernel;
use Drupal\Tests\protect_before_launch\Unit\UnitTestCase;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ProtectEventSubscriberTest
 *
 * @group tests
 * @package Drupal\Tests\protect_before_launch\Unit\StackMiddleware
 */
class ProtectHttpKernelTest extends UnitTestCase {

  public function testAccessGrantedWhenDisabled()
  {
    $response = $this->dispatchRequestWithConfig(new Request(), array_merge($this->getProtectedConfig(), [
      'protect' => Configuration::CONFIG_DISABLED
    ]));
    $this->assertEmpty($response);
  }

  public function testAccessDeniedWhenProtectedWithoutCredentials()
  {
    $response = $this->dispatchRequestWithConfig(new Request(), $this->getProtectedConfig());
    $this->assertProtected($response);
  }

  public function testAccessGrantedWhenProtectedWithValidCredentials()
  {
    $request = new Request();
    $request->headers->set('PHP_AUTH_USER', 'foo');
    $request->headers->set('PHP_AUTH_PW', 'bar');

    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'authentication_type' => Configuration::CONFIG_AUTH_SIMPLE,
      'username' => 'foo',
      'password' => password_hash('bar', PASSWORD_DEFAULT)
    ]));

    $this->assertEmpty($response);
  }

  public function testAccessDeniedWhenProtectedWithWrongCredentials()
  {
    $request = new Request();
    $request->headers->set('PHP_AUTH_USER', 'foo');
    $request->headers->set('PHP_AUTH_PW', 'ber');

    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'authentication_type' => Configuration::CONFIG_AUTH_SIMPLE,
      'username' => 'foo',
      'password' => password_hash('bar', PASSWORD_DEFAULT)
    ]));

    $this->assertProtected($response);
  }

  public function testAccessGrantedWhenProtectedWithoutCredentialsWithMatchingExcludePath()
  {
    $response = $this->dispatchRequestWithConfig(Request::create('/foo'), array_merge($this->getProtectedConfig(), [
      'exclude_paths' => '^/foo'
    ]));
    $this->assertEmpty($response);
  }

  public function testAccessDeniedWhenProtectedWithoutCredentialsWithoutMatchingExcludePath()
  {
    $response = $this->dispatchRequestWithConfig(Request::create('/foo'), array_merge($this->getProtectedConfig(), [
      'exclude_paths' => '^/bar'
    ]));
    $this->assertProtected($response);
  }

  public function testAccessDeniedWhenProtectedFromDrupal()
  {
    $request = new Request();
    $request->headers->set('PHP_AUTH_USER', 'foo');
    $request->headers->set('PHP_AUTH_PW', 'bar');

    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'authentication_type' => Configuration::CONFIG_AUTH_DRUPAL,
    ]));

    $this->assertEmpty($response);
  }

  public function testAccessDeniedWhenProtectedFromDrupalInvalidCredentials()
  {
    $request = new Request();
    $request->headers->set('PHP_AUTH_USER', 'foo');
    $request->headers->set('PHP_AUTH_PW', 'ber');

    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'authentication_type' => Configuration::CONFIG_AUTH_DRUPAL,
    ]));

    $this->assertProtected($response);
  }

  public function testAccessGrantedWhenNotProtectedByEnvironment()
  {
    $request = new Request();
    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'protect' => Configuration::CONFIG_ENV_ENABLED
    ]));

    $this->assertEmpty($response);
  }

  public function testAccessDeniedWhenProtectedByEnvironment()
  {
    putenv('PBF_ENABLED=1');

    $request = new Request();
    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'protect' => Configuration::CONFIG_ENV_ENABLED,
      'environment_key' => 'PBF_ENABLED'
    ]));

    $this->assertProtected($response);
  }

  public function testAccessGrantedWhenProtectedByEnvironmentWithValidCredentials()
  {
    putenv('PBF_ENABLED=1');

    $request = new Request();
    $request->headers->set('PHP_AUTH_USER', 'foo');
    $request->headers->set('PHP_AUTH_PW', 'bar');

    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'protect' => Configuration::CONFIG_ENV_ENABLED,
      'environment_key' => 'PBF_ENABLED'
    ]));

    $this->assertEmpty($response);
  }

  public function testAccessDeniedWhenProtectedByEnvironmentWithInvalidCredentials()
  {
    putenv('PBF_ENABLED=1');

    $request = new Request();
    $request->headers->set('PHP_AUTH_USER', 'foo');
    $request->headers->set('PHP_AUTH_PW', 'ber');

    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'protect' => Configuration::CONFIG_ENV_ENABLED,
      'environment_key' => 'PBF_ENABLED'
    ]));

    $this->assertProtected($response);
  }

  public function testAccessDeniedWhenProtectedByEnvironmentWithValue()
  {
    putenv('PBF_PROTECT=ENABLED');

    $request = new Request();
    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'protect' => Configuration::CONFIG_ENV_ENABLED,
      'environment_key' => 'PBF_PROTECT',
      'environment_value' => 'ENABLED'
    ]));

    $this->assertProtected($response);
  }

  public function testAccessGrantedWhenProtectedByEnvironmentWithValue()
  {
    putenv('PBF_PROTECT=DISABLED');

    $request = new Request();
    $response = $this->dispatchRequestWithConfig($request, array_merge($this->getProtectedConfig(), [
      'protect' => Configuration::CONFIG_ENV_ENABLED,
      'environment_key' => 'PBF_PROTECT',
      'environment_value' => 'ENABLED'
    ]));

    $this->assertEmpty($response);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Response $response
   */
  private function assertProtected(Response $response)
  {
    $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
    $this->assertEquals($response->getContent(), 'Access Denied');
    $this->assertContains('Secured Area', $response->headers->get('www-authenticate'));
    $this->assertContains('no-cache', $response->headers->get('cache-control'));
    $this->assertContains('private', $response->headers->get('cache-control'));
  }

  /**
   * @return array
   */
  private function getProtectedConfig()
  {
    return [
      'protect' => Configuration::CONFIG_ENABLED,
      'content' => 'Access Denied',
      'realm' => 'Secured Area'
    ];
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param array $config
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  private function dispatchRequestWithConfig(Request $request, array $config) {
    $configuration = new Configuration($this->getConfigFactoryStub([
      'protect_before_launch.settings' => $config,
    ]));

    $user = $this->createMock(UserInterface::class);
    $user->method('getPassword')->willReturn('bar');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadByProperties')->willReturn([$user]);

    $entityTypeManager = $this->createMock(EntityTypeManager::class);
    $entityTypeManager->method('getStorage')->willReturn($storage);

    $killSwitch = $this->createMock(KillSwitch::class);
    $password = $this->createMock(PasswordInterface::class);
    $password->method('check')->with($this->logicalOr(
      $this->equalTo('bar'),
      $this->equalTo('ber')
    ))->will($this->returnCallback(function ($value) {
      return $value === 'bar';
    }));

    $httpKernel = $this->createMock(HttpKernelInterface::class);

    $protectKernel = new ProtectHttpKernel($httpKernel, $configuration, $entityTypeManager, $killSwitch, $password);
    $response = $protectKernel->handle($request);

    return $response;
  }
}
