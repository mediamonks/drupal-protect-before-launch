<?php

namespace Drupal\Tests\protect_before_launch\Unit\Command;

use Drupal\protect_before_launch\Command\ProtectCommand;
use Drupal\protect_before_launch\Configuration;
use Drupal\Tests\protect_before_launch\Unit\UnitTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ProtectCommandTest
 *
 * @group tests
 * @package Drupal\Tests\protect_before_launch\Unit\Command
 */
class ProtectCommandTest extends UnitTestCase {

  /**
   * @dataProvider statusProvider
   * @param $status
   */
  public function testSetProtect($status) {
    $commandTester = new CommandTester($this->getCommand());
    $commandTester->execute(
      [
        'protect' => $status,
      ]
    );

    $this->assertContains('commands.protect_before_launch.protect.messages.success', $commandTester->getDisplay());
  }

  /**
   * @return array
   */
  public function statusProvider()
  {
    return [
      ['enable'],
      ['disable'],
      ['environment']
    ];
  }

  public function testSetProtectInteractive() {
    $this->verifySymfonyConsoleInputsSupport();

    $commandTester = new CommandTester($this->getCommand());
    $commandTester->setInputs([0]);
    $commandTester->execute([]);

    $this->assertContains('commands.protect_before_launch.protect.messages.question', $commandTester->getDisplay());
    $this->assertContains('commands.protect_before_launch.protect.messages.success', $commandTester->getDisplay());
  }

  private function getCommand()
  {
    $configuration = $this->createMock(Configuration::class);
    $configuration->expects($this->exactly(1))->method('setProtect')->willReturn(true);

    return new ProtectCommand($configuration);
  }
}
