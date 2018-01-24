<?php

namespace Drupal\protect_before_launch\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\protect_before_launch\Configuration;

/**
 * Class StatusCommand.
 *
 * @package Drupal\protect_before_launch
 *
 * @DrupalCommand (
 *     extension="protect_before_launch",
 *     extensionType="module"
 * )
 */
class StatusCommand extends AbstractCommand {

  const ARGUMENT_STATUS = 'status';

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('protect_before_launch:status')
      ->setAliases(['protect_before_launch:enabled'])
      ->addArgument(self::ARGUMENT_STATUS, InputArgument::OPTIONAL)
      ->setDescription($this->trans('commands.protect_before_launch.status.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    parent::execute($input, $output);

    $status = $input->getArgument(self::ARGUMENT_STATUS);
    $options = [
      'disable', 'disabled',
      'enable', 'enabled',
      'env_enabled', 'env_enable',
      'enable_env', 'enabled_env',
      'env', 'environment'
    ];
    if (!in_array($status, $options)) {
      $status = $this->getIo()->choice($this->trans('commands.protect_before_launch.status.messages.question'), [
        'disabled',
        'enabled',
        'environment'
      ]);
    }

    $this->getConfig()->setProtect($this->getStatusConfigCode($status));

    $this->getIo()->info(sprintf($this->trans('commands.protect_before_launch.status.messages.success'), $status));
  }

  /**
   * @param string $status
   *
   * @return int
   */
  private function getStatusConfigCode($status) {
    switch ($status) {
      case 'disable':
      case 'disabled':
        return Configuration::CONFIG_DISABLED;

      case 'enable':
      case 'enabled':
        return Configuration::CONFIG_ENABLED;

      default:
        return Configuration::CONFIG_ENV_ENABLED;
    }
  }

}
