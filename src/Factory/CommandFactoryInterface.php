<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\Factory;

use Bugloos\FaultToleranceBundle\Contract\Command;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
interface CommandFactoryInterface
{
    public function getCommand(...$args): Command;
}
