<?php

namespace Drupal\protect_before_launch\Service;

use Drupal\Core\Config\ConfigFactory;

/**
 * {@inheritdoc}
 */
class Configuration {

  /**
   * Config storage key.
   */
  const CONFIG_KEY = 'protect_before_launch.settings';

  /**
   * Set the config hash algorithm.
   */
  const CONFIG_HASH = PASSWORD_BCRYPT;

  /**
   * ConfigFactory for corage storage.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Configuration constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Public function configFactory.
   */
  public function __construct(ConfigFactory $configFactory) {
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
    $config
      ->set($key, $value)
      ->save();
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
   * Set, save and hash the password.
   *
   * @param string $password
   *   Public function setPassword password.
   *
   * @return $this
   *   Public function setPassword this.
   */
  public function setPassword($password) {
    if (strlen($password)) {
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
    return $this->get('protect') ? TRUE : FALSE;
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
    $this->set('realm', $content);
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
    return explode(PHP_EOL, $this->getExcludePathsText());
  }

  /**
   * Validate username and password against saved credentials.
   *
   * @param string $username
   *   Public function validate string username.
   * @param string $password
   *   Public function validate string password.
   *
   * @return bool
   *   Public function validate bool.
   */
  public function validate($username, $password) {
    if (!$username || !$password || $username != $this->getUsername() || !password_verify($password, $this->getPassword())) {
      return FALSE;
    }
    return TRUE;
  }

}
