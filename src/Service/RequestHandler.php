<?php

namespace Drupal\protect_before_launch\Service;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Password\PhpassHashedPassword;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class RequestHandler.
 *
 * @package Drupal\protect_before_launch
 */
class RequestHandler implements HttpKernelInterface {


  /**
   * Protected config.
   *
   * @var \Drupal\protect_before_launch\Service\Configuration
   */
  protected $config = NULL;

  /**
   * Protected httpKernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel = NULL;

  /**
   * Entity Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityManager = NULL;

  /**
   * Cache kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  protected $killSwitch = NULL;

  /**
   * RequestHandler constructor.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $httpKernel
   *   Public function httpKernel.
   * @param \Drupal\protect_before_launch\Service\Configuration $config
   *   Public function config.
   * @param \Drupal\Core\Entity\EntityTypeManager $entityManager
   *   Public function EntityManager.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   Public function KillSwitch.
   */
  public function __construct(HttpKernelInterface $httpKernel, Configuration $config, EntityTypeManager $entityManager, KillSwitch $killSwitch) {
    $this->httpKernel = $httpKernel;
    $this->config = $config;
    $this->entityManager = $entityManager;
    $this->killSwitch = $killSwitch;
  }

  /**
   * Shield pages is enabled status.
   *
   * @return bool
   *   Protected function shieldPage bool.
   */
  protected function shieldPage() {
    $status = $this->config->getProtect();
    if (Configuration::CONFIG_ENABLED == $status) {
      return TRUE;
    }
    elseif (Configuration::CONFIG_ENV_ENABLED == $status) {
      return $this->systemEnvEnableShield();
    }
  }

  /**
   * Check if to auto enable based on env variable.
   *
   * @return int
   *   Protection status
   */
  protected function systemEnvEnableShield() {
    if (FALSE !== getenv($this->config->getEnvironmentKey())) {
      if ($this->config->getEnvironmentValue()) {
        if (getenv($this->config->getEnvironmentKey()) == $this->config->getEnvironmentValue()) {
          return Configuration::CONFIG_ENABLED;
        }
        else {
          return Configuration::CONFIG_DISABLED;
        }
      }
      else {
        return Configuration::CONFIG_ENABLED;
      }
    }
    return Configuration::CONFIG_DISABLED;
  }

  /**
   * Authenticate username,password and select correct backend.
   *
   * @param string $username
   *   The username.
   * @param string $password
   *   The password.
   *
   * @return bool
   *   Return status
   */
  protected function authenticate($username, $password) {
    if (Configuration::CONFIG_AUTH_SIMPLE == $this->config->getAuthenticationType()) {
      return $this->authenticateSimple($username, $password);
    }
    else {
      return $this->authenticateDrupal($username, $password);
    }

  }

  /**
   * Authenticate user and password against simple username and password.
   *
   * @param string $username
   *   The username.
   * @param string $password
   *   The password.
   *
   * @return bool
   *   Return status.
   */
  protected function authenticateSimple($username, $password) {
    return $this->config->validate($username, $password);
  }

  /**
   * Authenticate username and password against drupal user database.
   *
   * @param string $username
   *   The username.
   * @param string $password
   *   The password.
   *
   * @return bool
   *   Return status.
   */
  protected function authenticateDrupal($username, $password) {
    try {
      $users = $this->entityManager->getStorage('user')
        ->loadByProperties(['name' => $username]);

      if (count($users) > 1 || count($users) < 1) {
        return FALSE;
      }

      $user = array_shift($users);

      $passwordInterface = new PhpassHashedPassword(PhpassHashedPassword::MIN_HASH_COUNT);
      return $passwordInterface->check($password, $user->getPassword());
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Check if path is excluded from password protection.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Protected function excludedPath request.
   *
   * @return bool
   *   Protected excludedPath bool.
   */
  protected function excludedPath(Request $request) {
    $currentPath = urldecode($request->getRequestUri());

    foreach ($this->config->getExcludePaths() as $path) {
      if (strlen(trim($path)) && preg_match('/' . str_replace('/', '\/', $path) . '/i', $currentPath)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Is user allowed to visit page if not display password.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Protected function isAllowed request.
   * @param \Drupal\Core\Render\HtmlResponse $response
   *   Protected function isAllowed response.
   *
   * @return \Drupal\Core\Render\HtmlResponse
   *   Protected function isAllowed.
   */
  protected function isAllowed(Request $request, HtmlResponse $response) {
    if ($this->shieldPage() && !$this->excludedPath($request) && !$this->authenticate($request->getUser(), $request->getPassword())) {
      $this->killSwitch->trigger();
      $response->headers->add(['WWW-Authenticate' => 'Basic realm="' . $this->config->getRealm() . '"']);
      $response->setStatusCode(401, 'Unauthorized');
      $response->setContent($this->config->getContent());
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    /** @var \Drupal\Core\Render\HtmlResponse $response */
    $response = $this->httpKernel->handle($request, $type, $catch);
    if ('cli' != php_sapi_name() && get_class($response) == 'Drupal\Core\Render\HtmlResponse') {
      $response = $this->isAllowed($request, $response);
    }
    return $response;
  }

}
