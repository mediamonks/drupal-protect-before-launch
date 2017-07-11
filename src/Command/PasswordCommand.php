<?php

namespace Drupal\protect_before_launch\Command;

use Drupal\protect_before_launch\Service\Configuration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Drupal\Console\Core\Command\Shared\CommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class PasswordCommand.
 *
 * @package Drupal\protect_before_launch
 *
 * @DrupalCommand (
 *     extension="protect_before_launch",
 *     extensionType="module"
 * )
 */
class PasswordCommand extends Command {

  use CommandTrait;

  /**
   * Constructs a new UsernameCommand object.
   *
   * @param $config ConfigFactory
   */
  protected $config;

  public function __construct(Configuration $config) {
    $this->config = $config;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('protect_before_launch:password')
      ->setDescription($this->trans('commands.protect_before_launch.password.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $password = $io->ask('Which password to set?');
    $this->config->setPassword($password);


    $io->info($this->trans('commands.protect_before_launch.password.messages.success'));
  }
}
