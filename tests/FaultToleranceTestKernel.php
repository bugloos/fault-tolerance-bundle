<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\Tests;

use Bugloos\FaultToleranceBundle\FaultToleranceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Exception;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
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
