<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\Factory;

use Bugloos\FaultToleranceBundle\Contract\Command;
use Bugloos\FaultToleranceBundle\RequestLog\RequestLog;
use Exception;
use ReflectionClass;
use ReflectionException;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class CommandFactory implements CommandFactoryInterface
{
    private CircuitBreakerFactory $circuitBreakerFactory;

    private RequestCacheFactory $requestCacheFactory;

    private RequestLog $requestLog;

    public function __construct(
        CircuitBreakerFactory $circuitBreakerFactory,
        RequestCacheFactory $requestCacheFactory,
        RequestLog $requestLog
    ) {
        $this->circuitBreakerFactory = $circuitBreakerFactory;
        $this->requestCacheFactory = $requestCacheFactory;
        $this->requestLog = $requestLog;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function getCommand(...$args): Command
    {
        $class = func_get_args()[0];

        $parameters = func_get_args();
        array_shift($parameters);

        $reflection = new ReflectionClass($class);

        /* @var Command $command */
        $command = empty($parameters) ?
            $reflection->newInstance() :
            $reflection->newInstanceArgs($parameters);

        $command->initializeConfig();
        $command->setCircuitBreakerFactory($this->circuitBreakerFactory);
        $command->setRequestCacheFactory($this->requestCacheFactory);
        $command->setRequestLog($this->requestLog);

        return $command;
    }
}
