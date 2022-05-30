<?php

namespace Bugloos\FaultToleranceBundle\Factory;

use Bugloos\FaultToleranceBundle\Contract\Command;

interface CommandFactoryInterface
{
    public function getCommand(...$args): Command;
}
