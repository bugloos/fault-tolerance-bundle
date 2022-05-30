<h2 align="center">
Fault Tolerance Bundle
</h2>

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/badges/build.png?b=main)](https://scrutinizer-ci.com/g/bugloos/fault-tolerance-bundle/build-status/main)

<h2>What does it do? :)</h2>

<p> - Circuit Breaker Pattern </p>
<p> - Cache each request that you want and saves in Redis or MongoDB for a specified time </p>
<p> - Logs every tings that happen, in a suitable format for performance and error monitoring </p>
<p> - Determine static fallback data for while that circuit breaker doesn't allow request to be executed and cache data doesn't exist for the request </p>

<h2>Installation</h2>

```bash
composer require bugloos/fault-tolerance-bundle
```

<h2>Compatibility</h2>

* PHP v7.4 or above
* Symfony v4.4 or above

<h3>What is circuit breaker pattern</h3>

<p>
In Microservices architecture, a service usually calls other services to retrieve data,
and there is the chance that the downstream service may be down. It may be cause 
by slow network connection, timeouts, or temporal unavailability. Therefore,
retrying calls can solve the issue. However, if there is a severe issue on 
a particular microservice, then it will be unavailable for a longer time. In such case,
the request will be continuously sent to that service, since the client doesn’t have
any knowledge about a particular service being down. As a result, the network resources
will be exhausted with low performance and bad user experience. Also, the failure of
one service might lead to Cascading failures throughout the application.
</p>

<p>
Therefore, you can use the Circuit Breaker Design Pattern to overcome this problem.
With the help of this pattern, the client will invoke a remote service through a proxy.
This proxy will basically behave as an electrical circuit breaker.
So, when the number of failures crosses the threshold number, the circuit breaker trips
for a particular time period. Then, all the attempts to invoke the remote service 
will fail within this timeout period. After the timeout expires, the circuit breaker 
allows a limited number of test requests to pass through it. If those requests succeed,
the circuit breaker resumes back to the normal operation. Otherwise, if there is a failure,
the timeout period begins again.
</p>
