<?php

namespace Drupal\Tests\protect_before_launch\Unit\Command;

use Drupal\protect_before_launch\Command\EnvironmentCommand;
use Drupal\protect_before_launch\Configuration;
use Drupal\Tests\protect_before_launch\Unit\UnitTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class EnvironmentCommandTest
 *
 * @group tests
 * @package Drupal\Tests\protect_before_launch\Unit\Command
 */
class EnvironmentCommandTest extends UnitTestCase {

  public function testSetKeyValue() {

    $configuration = $this->createMock(Configuration::class);
    $configuration->expects($this->exactly(1))->method('setEnvironmentKey')->willReturn(TRUE);
    $configuration->expects($this->exactly(1))->method('setEnvironmentValue')->willReturn(TRUE);

    $command = new EnvironmentCommand($configuration);

    $commandTester = new CommandTester($command);
    $commandTester->execute(
      [
        'key' => 'MY_KEY',
        'value' => 'MY_VALUE',
      ]
    );

    $this->assertContains('commands.protect_before_launch.environment.messages.success', $commandTester->getDisplay());
  }

  public function testSetKeyWithoutValue() {
    $this->verifySymfonyConsoleInputsSupport();

    $configuration = $this->createMock(Configuration::class);
    $configuration->expects($this->exactly(1))->method('setEnvironmentKey')->willReturn(TRUE);

    $command = new EnvironmentCommand($configuration);

    $commandTester = new CommandTester($command);
    $commandTester->setInputs([' ']);
    $commandTester->execute(
      [
        'key' => 'MY_KEY',
      ]
    );

    $this->assertContains('commands.protect_before_launch.environment.messages.success', $commandTester->getDisplay());
  }

  public function testSetKeyWithoutValueOption() {
    $configuration = $this->createMock(Configuration::class);
    $configuration->expects($this->exactly(1))->method('setEnvironmentKey')->willReturn(TRUE);

    $command = new EnvironmentCommand($configuration);

    $commandTester = new CommandTester($command);
    $commandTester->execute(
      [
        'key' => 'MY_KEY',
        '--no-value' => ' ',
      ]
    );

    $this->assertContains('commands.protect_before_launch.environment.messages.success', $commandTester->getDisplay());
  }
}
