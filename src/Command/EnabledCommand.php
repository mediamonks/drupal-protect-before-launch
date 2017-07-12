<?php

namespace Drupal\protect_before_launch\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Drupal\Console\Core\Command\Shared\CommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\Console\Annotations\DrupalCommand;
use Drupal\protect_before_launch\Service\Configuration;

/**
 * Class EnabledCommand.
 *
 * @package Drupal\protect_before_launch
 *
 * @DrupalCommand (
 *     extension="protect_before_launch",
 *     extensionType="module"
 * )
 */
class EnabledCommand extends Command {

  use CommandTrait;

  /**
   * Drupal\protect_before_launch\Service\Configuration definition.
   *
   * @var \Drupal\protect_before_launch\Service\Configuration
   */
  protected $config;

  /**
   * Constructs a new EnabledCommand object.
   */
  public function __construct(Configuration $config) {
    $this->config = $config;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('protect_before_launch:enabled')
      ->addArgument('enabled', InputArgument::OPTIONAL)
      ->setDescription($this->trans('commands.protect_before_launch.enabled.description'));
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $io = new DrupalStyle($input, $output);

    $enabled = $input->getArgument('enabled');

    if ($enabled != 'true' && $enabled != 'false') {
      $enabled = $io->choice('enable password protection', ['true', 'false']);
    }
    $this->config->setProtect($enabled == 'true' ? 1 : 0);

    $io->info(sprintf($this->trans('commands.protect_before_launch.enabled.messages.success'), $enabled == 'true' ? 'Enabled' : 'Disabled'));
  }

}
