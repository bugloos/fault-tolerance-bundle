<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\Contract;

use Bugloos\FaultToleranceBundle\Config\Config;
use Bugloos\FaultToleranceBundle\Enum\EventEnum;
use Bugloos\FaultToleranceBundle\Exception\FallbackNotAvailableException;
use Bugloos\FaultToleranceBundle\Exception\RuntimeException;
use Bugloos\FaultToleranceBundle\Factory\CircuitBreakerFactory;
use Bugloos\FaultToleranceBundle\Factory\RequestCacheFactory;
use Bugloos\FaultToleranceBundle\RequestCache\RequestCache;
use Bugloos\FaultToleranceBundle\RequestLog\RequestLog;
use LogicException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Psr\Cache\InvalidArgumentException;
use Exception;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
abstract class Command
{
    private CircuitBreakerFactory $circuitBreakerFactory;

    private RequestCacheFactory $requestCacheFactory;

    private RequestLog $requestLog;

    private array $config;

    /**
     * Command Key, used for grouping Circuit Breakers
     */
    protected string $commandKey = '';

    /**
     * Events logged during execution
     */
    private array $executionEvents = [];

    /**
     * Execution time in milliseconds
     */
    private int $executionTime;

    /**
     * Timestamp in milliseconds
     */
    private int $invocationStartTime;

    /**
     * Exception thrown if there was one
     */
    private \Exception $executionException;

    public function setCircuitBreakerFactory(CircuitBreakerFactory $circuitBreakerFactory)
    {
        $this->circuitBreakerFactory = $circuitBreakerFactory;
    }

    public function setRequestCacheFactory(RequestCacheFactory $requestCacheFactory)
    {
        $this->requestCacheFactory = $requestCacheFactory;
    }

    public function setRequestLog(RequestLog $requestLog)
    {
        $this->requestLog = $requestLog;
    }

    /**
     * Determines and returns command key, used for circuit breaker grouping
     */
    public function getCommandKey(): string
    {
        /* If the command key hasn't been defined in the class we use the current class name */
        if ($this->commandKey === '') {
            $this->commandKey = str_replace('\\', '.', get_class($this));
        }

        return $this->commandKey;
    }

    public function initializeConfig()
    {
        $this->config = $this->config() !== null ?
            $this->config()->toArray() :
            (new Config())->toArray();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function config(): ?Config
    {
        return null;
    }

    /**
     * Determines whether request caching is enabled for this command
     */
    private function isRequestCacheEnabled(): bool
    {
        return $this->config['requestCache']['enabled'] && $this->getCacheKey() !== null;
    }

    /**
     * @throws Exception|InvalidArgumentException|LogicException
     */
    public function execute()
    {
        $circuitBreaker = $this->getCircuitBreaker();

        $cacheEnabled = $this->isRequestCacheEnabled();

        $this->recordExecutedCommand();

        if ($cacheEnabled) {
            $requestCache = $this->getCacheRequest();
            $cacheExists = $requestCache->exists($this->getCommandKey(), $this->getCacheKey());
            if ($cacheExists) {
                $this->recordExecutionEvent(EventEnum::RESPONSE_FROM_CACHE);
                return $requestCache->get($this->getCommandKey(), $this->getCacheKey());
            }
        }
        if (! $circuitBreaker->allowRequest()) {
            $this->recordExecutionEvent(EventEnum::SHORT_CIRCUITED);
            return $this->getFallbackOrThrowException();
        }

        $this->invocationStartTime = $this->getTimeInMilliseconds();

        try {
            $result = $this->run();
            $this->recordExecutionTime();
            $circuitBreaker->markAsSuccess();
            $this->recordExecutionEvent(EventEnum::SUCCESS);
        } catch (ClientExceptionInterface $exception) {
            /* without any tracking or fallback logic */
            $this->recordExecutionTime();
            throw new LogicException('Logic exception on proxy command : ' . static::class);
        } catch (Exception $exception) {
            $this->recordExecutionTime();
            $circuitBreaker->markAsFailure();
            $this->executionException = $exception;
            $this->recordExecutionEvent(EventEnum::FAILURE);
            return $this->getFallbackOrThrowException($exception);
        }

        if ($cacheEnabled) {
            $requestCache->put(
                $this->getCommandKey(),
                $this->getCacheKey(),
                $result,
                $this->config['requestCache']['expiresCacheAfter']
            );
        }

        return $result;
    }

    /**
     * The code to be executed
     */
    abstract protected function run();

    /**
     * Custom logic proceeding event generation
     */
    protected function processExecutionEvent(string $eventName)
    {
    }

    /**
     * Logic to record events and exceptions as they take place
     */
    private function recordExecutionEvent(string $eventName): void
    {
        $this->executionEvents[] = $eventName;

        $this->processExecutionEvent($eventName);
    }

    /**
     * Attempts to retrieve fallback by calling getFallback
     *
     * @param Exception|null $originalException (Optional) If null, the request was short-circuited
     * @return array
     * @throws Exception
     */
    private function getFallbackOrThrowException(Exception $originalException = null)
    {
        $message = $originalException === null ? 'Short-circuited' : $originalException->getMessage();
        try {
            if (! $this->config['fallback']['enabled']) {
                throw new RuntimeException(
                    $message . ' and fallback disabled',
                    get_class($this),
                    $originalException
                );
            }
            try {
                $executionResult = $this->getFallback();
                $this->recordExecutionEvent(EventEnum::FALLBACK_SUCCESS);
                return $executionResult;
            } catch (FallbackNotAvailableException $fallbackException) {
                throw new RuntimeException(
                    $message . ' and no fallback available',
                    get_class($this),
                    $originalException
                );
            } catch (Exception $fallbackException) {
                $this->recordExecutionEvent(EventEnum::FALLBACK_FAILURE);
                throw new RuntimeException(
                    $message . ' and failed retrieving fallback',
                    get_class($this),
                    $originalException,
                    $fallbackException
                );
            }
        } catch (Exception $exception) {
            $this->recordExecutionEvent(EventEnum::EXCEPTION_THROWN);
            throw $exception;
        }
    }

    /**
     * Code for when execution fails for whatever reason
     *
     * @throws FallbackNotAvailableException When no custom fallback provided
     */
    protected function getFallback()
    {
        throw new FallbackNotAvailableException('No fallback available');
    }

    /**
     * Key to be used for request caching.
     *
     * By default this return null, which means "do not cache". To enable caching,
     * override this method and return a string key uniquely representing the state of a command instance.
     *
     * If multiple command instances are executed within current HTTP request, only the first one will be
     * executed and all others returned from cache.
     *
     * @return string|null
     */
    protected function getCacheKey(): ?string
    {
        return null;
    }

    /**
     * Returns events collected
     *
     * @return array
     */
    public function getExecutionEvents(): array
    {
        return $this->executionEvents;
    }

    /**
     * Returns execution time in milliseconds, null if not executed
     *
     * @return null|integer
     */
    public function getExecutionTimeInMilliseconds(): ?int
    {
        return $this->executionTime;
    }

    /**
     * Returns exception thrown while executing the command, if there was any
     *
     * @return Exception|null
     */
    public function getExecutionException(): ?Exception
    {
        return $this->executionException;
    }

    /**
     * Records command execution time if the command was executed, not short-circuited and not returned from cache
     */
    private function recordExecutionTime(): void
    {
        $this->executionTime = $this->getTimeInMilliseconds() - $this->invocationStartTime;
    }

    /**
     * Returns current time on the server in milliseconds
     *
     * @return float
     */
    private function getTimeInMilliseconds(): float
    {
        return floor(microtime(true) * 1000);
    }

    /**
     * Adds reference to the command to the current request log
     */
    private function recordExecutedCommand(): void
    {
        if ($this->isRequestLogEnabled()) {
            $this->requestLog->addExecutedCommand($this);
        }
    }

    private function isRequestLogEnabled(): bool
    {
        return $this->config['requestLog']['enabled'];
    }

    private function getCircuitBreaker()
    {
        return $this->circuitBreakerFactory->create(
            $this->getCommandKey(),
            $this->config['circuitBreaker']
        );
    }

    /**
     * @throws Exception
     */
    private function getCacheRequest(): RequestCache
    {
        return $this->requestCacheFactory->create(
            $this->config['requestCache']['storage']
        );
    }
}
