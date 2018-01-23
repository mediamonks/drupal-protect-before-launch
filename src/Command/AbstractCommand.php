<?php

namespace Drupal\protect_before_launch\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Drupal\Console\Core\Command\Shared\CommandTrait;
use Drupal\Console\Core\Style\DrupalStyle;
use Drupal\protect_before_launch\Configuration;

/**
 * Class AbstractCommand
 *
 * @package Drupal\protect_before_launch\Command
 */
abstract class AbstractCommand extends Command {

  use CommandTrait;

  /**
   * Drupal\protect_before_launch\Configuration definition.
   *
   * @var \Drupal\protect_before_launch\Configuration
   */
  private $config;

  /**
   * @var \Drupal\Console\Core\Style\DrupalStyle
   */
  private $io;

  /**
   * @param \Drupal\protect_before_launch\Configuration $config
   */
  public function __construct(Configuration $config) {
    $this->config = $config;
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->io = new DrupalStyle($input, $output);
  }

  /**
   * @return \Drupal\protect_before_launch\Configuration
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * @return \Drupal\Console\Core\Style\DrupalStyle
   */
  public function getIo() {
    return $this->io;
  }

}
