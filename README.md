<h2 align="center">
Fault Tolerance Bundle
</h2>

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/badges/build.png?b=main)](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/build-status/main)

<h3>What does it do? :)</h3>

*  Circuit Breaker Pattern 
*  Cache each request that you want and saves in Redis for a specified time
*  Determine static fallback data for while that circuit breaker doesn't allow request to be executed and cache data doesn't exist for the request

<h3>Installation</h3>

```bash
composer require bugloos/fault-tolerance-bundle
```

<h3>Compatibility</h3>

* PHP v7.4 or above
* Symfony v4.4 or above

<h4>What is circuit breaker pattern</h4>

In microservice architecture, a service usually calls other services to retrieve data,
and there is the chance that the downstream service may be down. It may be cause 
by slow network connection, timeouts, or temporal unavailability. Therefore,
retrying calls can solve the issue. However, if there is a severe issue on 
a particular microservice, then it will be unavailable for a longer time. In such case,
the request will be continuously sent to that service, since the client doesn’t have
any knowledge about a particular service being down. As a result, the network resources
will be exhausted with low performance and bad user experience. Also, the failure of
one service might lead to cascading failures throughout the application.

Therefore, you can use the Circuit Breaker Design Pattern to overcome this problem.
With the help of this pattern, the client will invoke a remote service through a proxy.
This proxy will basically behave as an electrical circuit breaker.
So, when the number of failures crosses the threshold number, the circuit breaker trips
for a particular time period. Then, all the attempts to invoke the remote service 
will fail within this timeout period. After the timeout expires, the circuit breaker 
allows a limited number of test requests to pass through it. If those requests succeed,
the circuit breaker resumes back to the normal operation. Otherwise, if there is a failure,
the timeout period begins again.


<h3>Usage</h3>

To protect a point of access to remote service, we use the command pattern. Here is how a minimal implementation could look like:
```php
namespace App\Proxy;

use Bugloos\FaultToleranceBundle\Contract\Command;

/**
* All commands must extend Fault Tolerance Bundle's Command
  */
class GetOrderProxyCommand extends Command
{
  private $param1;
  
  private $param2;

  public function __construct($param1, $param2)
  { 
      $this->param1 = $param1;
      $this->param2 = $param2;
  }

  /**
    * This function is called internally by Fault Tolerance Bundle, only if the request is allowed
    *
    * @return mixed
    */
  protected function run()
  {
      # Make an HTTP call
  }
}
```

This command could be used like this:

```php
use Bugloos\FaultToleranceBundle\Factory\CommandFactoryInterface;
use App\Proxy\GetOrderProxyCommand;

class Service
{
  private CommandFactoryInterface $commandFactory;

  public function __construct(CommandFactoryInterface $commandFactory)
  {
      $this->commandFactory = $commandFactory;
  }

  public function getAvatarUrl()
  {
      $getOrderProxyCommand = $this->commandFactory->getCommand(
          GetOrderProxyCommand::class,
          'param1',
          'param2'
      );
      
      $result = $getOrderProxyCommand->execute();
  }
}
```
Note: the extra parameters you pass to the factory’s getCommand method are forwarded to the command’s constructor.
####
Command-specific configurations are merged with the default one on instantiation. “GetOrderProxyCommand” in this case is the command key. By default, it is the same as command’s class, but you can set it yourself by overriding the getCommandKey protected method:

```php
    /**
     * @return string
     */
    protected function getCommandKey()
    {
        return 'CustomCommandKey';
    }
```
Fault tolerant bundle only works with the command keys. If you have two different commands with the same command key - Fault tolerant will disable and enable requests, as for a single entity. This may be used for grouping commands.

####

To manage configuration for each command you can use Config object in config protected method in command class:

```php
    /**
     * @return ?Bugloos\FaultToleranceBundle\Config\Config
     */
    protected function config()
    {
        return (new Config())
            ->intervalToHalfOpen(15)
            ->failureRateThreshold(20)
            ->timeWindow(60);
    }
```

Note: the config you set is merged with the default configs.

<h3>Request Cache</h3>

Request cache, when enabled, caches command execution result within a single HTTP request, so you don’t have to worry about loading data over network more than needed.

Results are cached per command key per cache key. To define cache key generation logic, implement getCacheKey protected method:

```php
    protected function getCacheKey()
    {
        return 'cache_' . $user;
    }
```

<h3>Fallback</h3>

For a command, you can specify fallback logic, that will be executed in case of a failure, or when the remote service is blocked:

```php
namespace App\Proxy;

use Bugloos\FaultToleranceBundle\Contract\Command;

class GetAvatarUrlProxyCommand extends Command
{

    protected function run()
    {
        # Make an HTTP call
    }
  
    /**
     * When __run__ fails for some reason, or when Fault Tolerant doesn't allow the request in the first place,
     * this function result will be returned instead
     *
     * @return string
     */
    protected function getFallback()
    {
        // we failed getting user's picture, so showing a generic no-photo placeholder instead.
        return 'https://example.com/avatars/fallback-image.jpg';
    }
}
```
Note: If you want to use logic requiring networking for your fallback, make sure to “wrap” it into a Fault tolerant command of its own

####
