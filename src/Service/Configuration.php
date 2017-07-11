<?php

namespace Drupal\protect_before_launch\Service;


use Drupal\Core\Config\ConfigFactory;

class Configuration {

  const config_key = 'protect_before_launch.settings';

  protected  $configFactory;


  public function __construct(ConfigFactory $configFactory) {
    $this->configFactory = $configFactory;
  }

  protected function get($key){
    $config = $this->configFactory->get(self::config_key);
    return $config->get($key);
  }

  protected function set($key, $value){
    $config = $this->configFactory->getEditable(self::config_key);
    $config
      ->set($key, $value)
      ->save();
    return this;
  }

  public function getUsername(){
    return $this->get('username');
  }

  public function getPassword(){
    return $this->get('password');
  }

  public function getRealm(){
    return str_replace('"', '\"', $this->get('realm'));
  }

  public function getContent(){
    return $this->get('content');
  }

  public function getProtect(){
    return $this->get('protect') ? true : false;
  }

  public function setProtect($protect){
    $this->set('protect', $protect);
    return $this;
  }

  public function setUsername($username){
    $this->set('username', $username);
    return $this;
  }

  public function setPassword($password){
    if(strlen($password)){
      $this->set('password', password_hash($password, PASSWORD_BCRYPT));
    }
    return $this;
  }

  public function setRealm($realm){
    $this->set('realm', $realm);
    return $this;

  }

  public function setContent($content){
    $this->set('realm', $content);
    return $this;
  }

  public function setExcludePaths($paths){
    $this->set('exclude_paths', str_replace("\r", '', $paths));
    return $this;
  }

  public function getExcludePathsText(){
   return $this->get('exclude_paths');
  }

  public function getExcludePaths(){
   return explode(PHP_EOL, $this->getExcludePathsText());
  }

  public function validate($username, $password){
    if(!$username || !$password || $username != $this->getUsername() || !password_verify($password, $this->getPassword())){
      return false;
    }
    return true;
  }
}