<?php
declare(strict_types=1);

namespace App\CompilerPasses;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RegisterListenersPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container): void
    {
    }
}
