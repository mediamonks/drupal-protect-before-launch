<?php

namespace Drupal\protect_before_launch;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * {@inheritdoc}
 */
class Configuration {

  /**
   * Config storage key.
   */
  const CONFIG_KEY = 'protect_before_launch.settings';

  /**
   * Always disabled.
   */
  const CONFIG_DISABLED = 0;

  /**
   * Always enabled.
   */
  const CONFIG_ENABLED = 1;

  /**
   * Enabled only when env variable is set.
   */
  const CONFIG_ENV_ENABLED = 2;

  /**
   * Simple authenticate.
   */
  const CONFIG_AUTH_SIMPLE = 1;

  /**
   * Drupal authenticate.
   */
  const CONFIG_AUTH_DRUPAL = 2;

  /**
   * Set the config hash algorithm.
   */
  const CONFIG_HASH = PASSWORD_BCRYPT;

  /**
   * ConfigFactory for corage storage.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Configuration constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Public function configFactory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Generic getter config values.
   *
   * @param string $key
   *   Protected string key.
   *
   * @return array|mixed|null
   *   Protected function array null.
   */
  protected function get($key) {
    $config = $this->configFactory->get(self::CONFIG_KEY);
    return $config->get($key);
  }

  /**
   * Generic setter config values.
   *
   * @param string $key
   *   Protected function set string key.
   * @param array|mixed|null $value
   *   Protected function array value.
   *
   * @return $this
   *   Protected function set this.
   */
  protected function set($key, $value) {
    $config = $this->configFactory->getEditable(self::CONFIG_KEY);
    $config->set($key, $value);
    $config->save();
    return $this;
  }

  /**
   * Set and save the username.
   *
   * @param string $username
   *   Public function setUsername string username.
   *
   * @return $this
   *   Public function setUsername this.
   */
  public function setUsername($username) {
    $this->set('username', $username);
    return $this;
  }

  /**
   * Get the username.
   *
   * @return string|null
   *   Public function getUsername string null.
   */
  public function getUsername() {
    return $this->get('username');
  }

  /**
   * Set, hash and save the password.
   *
   * @param string $password
   *   Public function setPassword password.
   *
   * @return $this
   *   Public function setPassword this.
   */
  public function setPassword($password) {
    if (!empty($password)) {
      $this->set('password', password_hash($password, self::CONFIG_HASH));
    }
    return $this;
  }

  /**
   * Get the hashed password.
   *
   * @return string|null
   *   Public function getPassword string.
   */
  public function getPassword() {
    return $this->get('password');
  }

  /**
   * Set and save the realm.
   *
   * @param mixed $realm
   *   Public function setRealm realm.
   *
   * @return $this
   *   Public function setRealm this.
   */
  public function setRealm($realm) {
    $this->set('realm', $realm);
    return $this;

  }

  /**
   * Get the Realm.
   *
   * @return string|null
   *   Public function getRealm string.
   */
  public function getRealm() {
    return str_replace('"', '\"', $this->get('realm'));
  }

  /**
   * Set protect status.
   *
   * @param mixed $protect
   *   Public function setProtect protect.
   *
   * @return $this
   *   Public functin setProtect this.
   */
  public function setProtect($protect) {
    $this->set('protect', $protect);
    return $this;
  }

  /**
   * Get protect status.
   *
   * @return bool
   *   Public function getProtect bool.
   */
  public function getProtect() {
    return $this->get('protect');
  }

  /**
   * Set escape content.
   *
   * @param string $content
   *   Public function setContent content.
   *
   * @return $this
   *   Public function setContent this.
   */
  public function setContent($content) {
    $this->set('content', $content);
    return $this;
  }

  /**
   * Get the escape content.
   *
   * @return string|null
   *   Public function getContent string.
   */
  public function getContent() {
    return $this->get('content');
  }

  /**
   * Set the Environment Key.
   *
   * @param string $environmentKey
   *   Public function setEnvironmentKey environmentKey.
   *
   * @return $this
   *   Public function setContent this.
   */
  public function setEnvironmentKey($environmentKey) {
    $this->set('environment_key', trim($environmentKey));
    return $this;
  }

  /**
   * Get Environment Key.
   *
   * @return string|null
   *   Public function setContent this.
   */
  public function getEnvironmentKey() {
    return $this->get('environment_key');
  }

  /**
   * Set the Environment Value.
   *
   * @param string $environmentValue
   *   Public function setEnvironmentValue environmentValue.
   *
   * @return $this
   *   Public function setEnvironmentValue this.
   */
  public function setEnvironmentValue($environmentValue) {
    $this->set('environment_value', trim($environmentValue));
    return $this;

  }

  /**
   * Get Environment Value.
   *
   * @return string|null
   *   Public function getEnvironmentValue this.
   */
  public function getEnvironmentValue() {
    return $this->get('environment_value');
  }

  /**
   * Set the Authentication Value.
   *
   * @param string $authenticationType
   *   Public function setAuthenticationType authenticationType.
   *
   * @return $this
   *   Public function setAuthenticationType this.
   */
  public function setAuthenticationType($authenticationType) {
    $this->set('authentication_type', $authenticationType);
    return $this;

  }

  /**
   * Get Authentication Value.
   *
   * @return string|null
   *   Public function getEnvironmentValue this.
   */
  public function getAuthenticationType() {
    return $this->get('authentication_type');
  }

  /**
   * Set exclude paths.
   *
   * @param string $paths
   *   Public function setExcludePaths string paths.
   *
   * @return $this
   *   Public function setExcludePaths this.
   */
  public function setExcludePaths($paths) {
    $this->set('exclude_paths', str_replace("\r", '', $paths));
    return $this;
  }

  /**
   * Get the exclude paths as text.
   *
   * @return string|null
   *   Public function getExcludePathsText string.
   */
  public function getExcludePathsText() {
    return $this->get('exclude_paths');
  }

  /**
   * Get the exclude paths as an array.
   *
   * @return array
   *   Public function getExcludePaths array.
   */
  public function getExcludePaths() {
    $paths = [];
    foreach (explode(PHP_EOL, $this->getExcludePathsText()) as $path) {
      if (!empty(trim($path))) {
        $paths[] = $path;
      }
    }
    return $paths;
  }

  /**
   * Verify username and password against saved credentials.
   *
   * @param string $username
   *   Public function validate string username.
   * @param string $password
   *   Public function validate string password.
   *
   * @return bool
   *   Public function validate bool.
   */
  public function validateCredentials($username, $password) {
    if (empty($username)
      || empty($password)
      || empty($this->getUsername())
      || empty($this->getPassword())
      || !hash_equals($this->getUsername(), $username)
      || !password_verify($password, $this->getPassword())
    ) {
      return FALSE;
    }
    return TRUE;
  }

}
