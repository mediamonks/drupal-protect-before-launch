<?php

namespace Drupal\protect_before_launch\Service;

use Drupal\Core\Config\ConfigFactory;

class Configuration {

  /**
   * Config storage key
   */
  const config_key = 'protect_before_launch.settings';

  /**
   * Set the config hash algorithm.
   */
  const config_hash = PASSWORD_BCRYPT;

  /**
   * ConfigFactory for corage storage
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Configuration constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   */
  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Generic getter config values.
   *
   * @param string $key
   *
   * @return array|mixed|null
   */
  protected function get($key) {
    $config = $this->configFactory->get(self::config_key);
    return $config->get($key);
  }

  /**
   * Generic setter config values.
   *
   * @param string $key
   * @param array|mixed|null $value
   *
   * @return $this
   */
  protected function set($key, $value) {
    $config = $this->configFactory->getEditable(self::config_key);
    $config
      ->set($key, $value)
      ->save();
    return $this;
  }

  /**
   * Set and save the username
   *
   * @param string $username
   *
   * @return $this
   */
  public function setUsername($username) {
    $this->set('username', $username);
    return $this;
  }

  /**
   * Get the username
   *
   * @return string|null
   */
  public function getUsername() {
    return $this->get('username');
  }

  /**
   * Set, save and hash the password
   *
   * @param $password
   *
   * @return $this
   */
  public function setPassword($password) {
    if (strlen($password)) {
      $this->set('password', password_hash($password, self::config_hash));
    }
    return $this;
  }

  /**
   * Get the hashed password
   *
   * @return string|null
   */
  public function getPassword() {
    return $this->get('password');
  }

  /**
   * Set and save the realm
   *
   * @param $realm
   *
   * @return $this
   */
  public function setRealm($realm) {
    $this->set('realm', $realm);
    return $this;

  }

  /**
   * Get the Realm
   *
   * @return string|null
   */
  public function getRealm() {
    return str_replace('"', '\"', $this->get('realm'));
  }

  /**
   * Set protect status
   *
   * @param $protect
   *
   * @return $this
   */
  public function setProtect($protect) {
    $this->set('protect', $protect);
    return $this;
  }

  /**
   * Get protect status
   *
   * @return bool
   */
  public function getProtect() {
    return $this->get('protect') ? TRUE : FALSE;
  }


  /**
   * Set escape content
   *
   * @param string $content
   *
   * @return $this
   */
  public function setContent($content) {
    $this->set('realm', $content);
    return $this;
  }

  /**
   * Get the escape content
   *
   * @return string|null
   */
  public function getContent() {
    return $this->get('content');
  }

  /**
   * Set exclude paths
   *
   * @param string $paths
   *
   * @return $this
   */
  public function setExcludePaths($paths) {
    $this->set('exclude_paths', str_replace("\r", '', $paths));
    return $this;
  }

  /**
   * Get the exclude paths as text
   *
   * @return string|null
   */
  public function getExcludePathsText() {
    return $this->get('exclude_paths');
  }

  /**
   * Get the exclude paths as an array
   *
   * @return array
   */
  public function getExcludePaths() {
    return explode(PHP_EOL, $this->getExcludePathsText());
  }

  /**
   * Validate username and password against saved credentials
   *
   * @param string $username
   * @param string $password
   *
   * @return bool
   */
  public function validate($username, $password) {
    if (!$username || !$password || $username != $this->getUsername() || !password_verify($password, $this->getPassword())) {
      return FALSE;
    }
    return TRUE;
  }
}