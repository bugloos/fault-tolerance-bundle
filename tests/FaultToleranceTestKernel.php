<?php

namespace Bugloos\FaultToleranceBundle\Tests;

use Bugloos\FaultToleranceBundle\FaultToleranceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Exception;

class FaultToleranceTestKernel extends Kernel
{
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new FaultToleranceBundle(),
        ];
    }

    /**
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/../src/Resources/config/services.yaml');
        $loader->load(__DIR__ . '/Fixtures/Config/config.yaml');
    }
}
