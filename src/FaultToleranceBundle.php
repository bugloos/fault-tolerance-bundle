<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle;

use Bugloos\FaultToleranceBundle\DependencyInjection\FaultToleranceExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class FaultToleranceBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new FaultToleranceExtension();
        }

        return $this->extension;
    }
}
