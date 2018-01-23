<?php

namespace Drupal\protect_before_launch\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Drupal\Console\Core\Command\Shared\CommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\protect_before_launch\Configuration;
use Drupal\Console\Annotations\DrupalCommand;

/**
 * Class EnvironmentCommand.
 *
 * @package Drupal\protect_before_launch
 *
 * @DrupalCommand (
 *     extension="protect_before_launch",
 *     extensionType="module"
 * )
 */
class EnvironmentCommand extends AbstractCommand {

  const ARGUMENT_KEY = 'key';
  const ARGUMENT_VALUE = 'value';
  const OPTION_NO_VALUE = 'no-value';

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('protect_before_launch:environment')
      ->addArgument(self::ARGUMENT_KEY, InputArgument::OPTIONAL)
      ->addArgument(self::ARGUMENT_VALUE, InputArgument::OPTIONAL)
      ->addOption(self::OPTION_NO_VALUE, null, InputOption::VALUE_NONE)
      ->setDescription($this->trans('commands.protect_before_launch.environment.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    parent::execute($input, $output);

    $key = $input->getArgument(self::ARGUMENT_KEY);
    if (empty($key)) {
      $key = $this->getIo()->ask($this->trans('commands.protect_before_launch.environment.messages.question_key'));
    }

    $this->getConfig()->setEnvironmentKey($key);

    $value = '';
    $noValue = $input->getOption(self::OPTION_NO_VALUE);
    if (!$noValue) {
      $value = $input->getArgument(self::ARGUMENT_VALUE);
      if (empty($value)) {
        $value = $this->getIo()->ask($this->trans('commands.protect_before_launch.environment.messages.question_value'), false);
      }

      if (!empty(trim($value))) {
        $this->getConfig()->setEnvironmentValue($value);
      }
    }

    $this->getIo()->info(sprintf($this->trans('commands.protect_before_launch.environment.messages.success'), $key, $value));
  }

}
