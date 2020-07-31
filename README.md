# Proxy generator for Symfony's DIC

[![Release](https://img.shields.io/packagist/v/olvlvl/symfony-dependency-injection-proxy.svg)](https://packagist.org/packages/olvlvl/symfony-dependency-injection-proxy)
[![Build Status](https://img.shields.io/travis/olvlvl/symfony-dependency-injection-proxy.svg)](http://travis-ci.org/olvlvl/symfony-dependency-injection-proxy)
[![Code Quality](https://img.shields.io/scrutinizer/g/olvlvl/symfony-dependency-injection-proxy.svg)](https://scrutinizer-ci.com/g/olvlvl/symfony-dependency-injection-proxy)
[![Code Coverage](https://img.shields.io/coveralls/olvlvl/symfony-dependency-injection-proxy.svg)](https://coveralls.io/r/olvlvl/symfony-dependency-injection-proxy)
[![Packagist](https://img.shields.io/packagist/dt/olvlvl/symfony-dependency-injection-proxy.svg)](https://packagist.org/packages/olvlvl/symfony-dependency-injection-proxy)

This package provides a proxy generator for [Symfony's dependency injection component][1] that generates super tiny,
super simple proxies, especially when [compared to Symphony's default implementation][2].

> If you're not familiar with proxy services, better have a look at [Symfony's documentation][3] before going any
> further.

The generator works with the following assumptions: the service we want to proxy implements an interface and services
using that service expect that interface too. Pretty normal stuff. Consider the following code, where an
`ExceptionHandler` service requires a logger implementing `LogInterface`:

```php
<?php

use Psr\Log\LoggerInterface;

class ExceptionHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    // …
}
```

Now imagine we're using [Monolog](https://github.com/Seldaek/monolog) as a logger, and we have an expansive stream to
setup. Why waste time building that logger for every request when it's seldom used? That's when we mark our service as
_lazy_.

The following example demonstrates how we can mark our `Psr\Log\LoggerInterface` service as lazy (we could use PHP code
or XML just the same):

```yaml
services:

  Psr\Log\LoggerInterface:
    class: Monolog\Logger
    lazy: true
    # …
```

The service can also use a factory:

```yaml
services:

  Psr\Log\LoggerInterface:
    factory: 'LoggerFactory::build'
    lazy: true
    # …
```

> We don't have to define our service with a class, we could use `logger` instead of `Psr\Log\LoggerInterface` just
> the same, except we would have to define `class` for the factory one.

Now let's see how to build our container.

## Building the dependency injection container

The following code demonstrates how to build, compile, and dump a container:

```php
<?php

use olvlvl\SymfonyDependencyInjectionProxy\ProxyDumper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

$builder = new ContainerBuilder();

// …
// Here we load our config, or build the container using clever PHP calls.
// We might event have some compile passes to add.
// …

$builder->compile();

$dumper = new PhpDumper($builder);
$dumper->setProxyDumper(new ProxyDumper());

/* @var string $containerFile */

file_put_contents($containerFile, $dumper->dump());
```

There you have it. We can use our container as usual and everything is awesome and cute.




### What if my lazy service implements multiple interfaces?

The basic interface resolver will have a hard time figuring out which interface to implement if a service implements
many. For instance, if a service was an instance of `ArrayObject` the following exception would be raised:

```
Don't know which interface to choose from for ArrayObject: IteratorAggregate, Traversable, ArrayAccess, Serializable, Countable.
```

We can specify the interface to implement using the `lazy` attribute:

```yaml
ArrayObject:
  lazy: ArrayAccess
```





----------





## Requirements

The package requires PHP 7.2.5 or later.





## Installation

The recommended way to install this package is through [Composer](http://getcomposer.org/):

	$ composer require olvlvl/symfony-dependency-injection-proxy





### Cloning the repository

The package is [available on GitHub](https://github.com/olvlvl/symfony-dependency-injection-proxy),
its repository can be cloned with the following command line:

	$ git clone https://github.com/olvlvl/symfony-dependency-injection-proxy.git





## Testing

The test suite is ran with the `make test` command. [PHPUnit](https://phpunit.de/) and
[Composer](http://getcomposer.org/) need to be globally available to run the suite. The command
installs dependencies as required. The `make test-coverage` command runs test suite and also creates
an HTML coverage report in `build/coverage`. If your environment doesn't meet the requirements you can run the tests
with a container, run `make test-container` to create it.

The package is continuously tested by [Travis CI](http://about.travis-ci.org/).

[![Build Status](https://img.shields.io/travis/olvlvl/symfony-dependency-injection-proxy.svg)](http://travis-ci.org/olvlvl/symfony-dependency-injection-proxy)
[![Code Coverage](https://img.shields.io/coveralls/olvlvl/symfony-dependency-injection-proxy.svg)](https://coveralls.io/r/olvlvl/symfony-dependency-injection-proxy)





## License

**olvlvl/symfony-dependency-injection-proxy** is licensed under the New BSD License - See the [LICENSE](LICENSE) file for details.






[1]: https://symfony.com/doc/current/components/dependency_injection.html
[2]: https://github.com/olvlvl/symfony-dependency-injection-proxy/wiki/Comparing-olvlvl's-proxy-generator-with-Symphony's
[3]: https://symfony.com/doc/current/service_container/lazy_services.html
