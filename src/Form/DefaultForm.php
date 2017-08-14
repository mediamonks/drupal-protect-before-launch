<?php

namespace Drupal\protect_before_launch\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\protect_before_launch\Service\Configuration;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DefaultForm.
 *
 * @package Drupal\protect_before_launch\Form
 */
class DefaultForm extends ConfigFormBase {

  /**
   * Protected config.
   *
   * @var \Drupal\protect_before_launch\Service\Configuration
   */
  protected $config;

  /**
   * Constructs a \Drupal\system\ConfigFormBase object.
   *
   * @param \Drupal\protect_before_launch\Service\Configuration $config
   *   The factory for configuration objects.
   */
  public function __construct(Configuration $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('protect_before_launch.configuration')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      Configuration::CONFIG_KEY,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['protect'] = [
      '#type' => 'select',
      '#title' => $this->t('Enable protection'),
      '#default_value' => $this->config->getProtect(),
      '#required' => TRUE,
      '#options' => [
        Configuration::CONFIG_DISABLED => $this->t('Disabled'),
        Configuration::CONFIG_ENABLED => $this->t('Enabled'),
        Configuration::CONFIG_ENV_ENABLED => $this->t('Auto Enabled with Environment key/value'),
      ],
      '#description' => $this->t('Enable the login for the site.'),
    ];

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => $this->config->getUsername(),
      '#required' => TRUE,
      '#description' => $this->t('The username to use to login.'),
    ];

    $form['password'] = [
      '#type' => 'password_confirm',
      '#required' => FALSE,
      '#description' => $this->t('The password to use for the login'),
    ];

    $form['advanced-section'] = [
      '#type' => 'details',
      '#title' => t('Advanced settings'),
      '#group' => 'advanced',
    ];

    $form['advanced-section']['authentication_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Identity provider'),
      '#default_value' => $this->config->getAuthenticationType(),
      '#required' => TRUE,
      '#options' => [
        Configuration::CONFIG_AUTH_SIMPLE => $this->t('Standalone Username and password'),
        Configuration::CONFIG_AUTH_DRUPAL => $this->t('Drupal authenticate'),
      ],
      '#description' => $this->t('Select identity provider'),
    ];

    $form['advanced-section']['realm'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Realm'),
      '#default_value' => $this->config->getRealm() ?: $this->t('This site is protected'),
      '#required' => TRUE,
      '#description' => $this->t('The realm for the password'),
    ];

    $form['advanced-section']['denied_content'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Denied content'),
      '#default_value' => $this->config->getContent() ?: $this->t('Access denied'),
      '#required' => TRUE,
      '#description' => $this->t('Text shown when user presses escape.'),
    ];

    $form['advanced-section']['exclude'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Exclude paths'),
      '#default_value' => $this->config->getExcludePathsText(),
      '#required' => FALSE,
      '#description' => $this->t('Exclude these paths from password protection. Preg match <a href="http://php.net/preg_match" target="_blank">Patterns</a>'),
    ];

    $form['advanced-section']['environment_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Environment key'),
      '#default_value' => $this->config->getEnvironmentKey() ?: 'AH_NON_PRODUCTION',
      '#required' => TRUE,
      '#description' => $this->t('The Environment variable to auto enable'),
    ];

    $form['advanced-section']['environment_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('environment_value key'),
      '#default_value' => $this->config->getEnvironmentValue(),
      '#required' => FALSE,
      '#description' => $this->t('The Environment value to auto enable. leave empty if you want to ignore the value'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Save configuration'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\protect_before_launch\Service\Configuration $config */
    $config = \Drupal::service('protect_before_launch.configuration');
    $config
      ->setUsername($form_state->getValue('username'))
      ->setAuthenticationType($form_state->getValue('authentication_type'))
      ->setRealm($form_state->getValue('realm'))
      ->setContent($form_state->getValue('denied_content'))
      ->setExcludePaths($form_state->getValue('exclude'))
      ->setProtect($form_state->getValue('protect'))
      ->setPassword($form_state->getValue('password'))
      ->setEnvironmentKey($form_state->getValue('environment_key'))
      ->setEnvironmentValue($form_state->getValue('environment_value'));
  }

}
