<?php

namespace Drupal\Tests\protect_before_launch\Unit;

use Drupal\Tests\UnitTestCase as BaseUnitTestCase;
use Symfony\Component\HttpKernel\Kernel;

class UnitTestCase extends BaseUnitTestCase
{
  use PhpunitCompatibilityTrait;

  public function verifySymfonyConsoleInputsSupport()
  {
    if (version_compare(Kernel::VERSION, '3.2', '<')) {
      $this->markTestSkipped('Requires Symfony 3.2');
    }
  }
}
