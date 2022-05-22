# Proxy generator for Symfony's DIC

[![Release](https://img.shields.io/packagist/v/olvlvl/symfony-dependency-injection-proxy.svg)](https://packagist.org/packages/olvlvl/symfony-dependency-injection-proxy)
[![Packagist](https://img.shields.io/packagist/dt/olvlvl/symfony-dependency-injection-proxy.svg)](https://packagist.org/packages/olvlvl/symfony-dependency-injection-proxy)
[![Code Quality](https://img.shields.io/scrutinizer/g/olvlvl/symfony-dependency-injection-proxy.svg)](https://scrutinizer-ci.com/g/olvlvl/symfony-dependency-injection-proxy)
[![Code Coverage](https://img.shields.io/coveralls/olvlvl/symfony-dependency-injection-proxy.svg)](https://coveralls.io/r/olvlvl/symfony-dependency-injection-proxy)

This package provides a proxy generator for [Symfony's dependency injection component][1] that
generates super tiny, super simple proxies, especially when [compared to Symfony's default
implementation][2]. Here are some differences:

- Can proxy `final` classes.
- Can only proxy classes with interfaces.
- The generated proxies are self-contained.
- The package is ~10Kb and doesn't have dependencies, other than `symfony/dependency-injection` of course.
- The package can be removed once the proxies have been generated.

If you're not familiar with proxy services, better have a look at [Symfony's documentation][3]
before going any further.



#### Installation

```bash
composer require olvlvl/symfony-dependency-injection-proxy
```



## How it works

The generator works with the following assumptions: the service we want to proxy implements an
interface, and services using that service expect that interface, following the [dependency
inversion principle][4]. Now, consider the following code, where an `ExceptionHandler` service
requires a logger implementing `LoggerInterface`:

```php
<?php

use Psr\Log\LoggerInterface;

class ExceptionHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    // …
}
```

Imagine we're using [Monolog](https://github.com/Seldaek/monolog) as logger, and we have an
expansive stream to set up. Why waste time building the logger for every request when it's seldom
used? That's when we mark our service as _lazy_.

The following example demonstrates how we can mark our `Psr\Log\LoggerInterface` service as lazy (we
could use PHP code or XML just the same):

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

**Note:** We don't have to define our service with a class, we could use `logger` instead of
`Psr\Log\LoggerInterface` just > the same, except we would have to define `class` for the factory
one.

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
// We might even have some compiler passes to add.
// …

$builder->compile();

$dumper = new PhpDumper($builder);
$dumper->setProxyDumper(new ProxyDumper());

/* @var string $containerFile */

file_put_contents($containerFile, $dumper->dump());
```

There you have it. We can use our container as usual and everything is awesome.



### What if my lazy service implements multiple interfaces?

The basic interface resolver will have a hard time figuring out which interface to implement if a
service implements many. For instance, if a service was an instance of `ArrayObject` the following
exception would be thrown:

```
Don't know which interface to choose from for ArrayObject: IteratorAggregate, Traversable, ArrayAccess, Serializable, Countable.
```

We can specify the interface to implement using the `lazy` attribute:

```yaml
ArrayObject:
  lazy: ArrayAccess
```



----------



## Continuous Integration

The project is continuously tested by [GitHub actions](https://github.com/olvlvl/symfony-dependency-injection-proxy/actions).

[![Tests](https://github.com/olvlvl/symfony-dependency-injection-proxy/workflows/test/badge.svg?branch=master)](https://github.com/olvlvl/symfony-dependency-injection-proxy/actions?query=workflow%3Atest)
[![Static Analysis](https://github.com/olvlvl/symfony-dependency-injection-proxy/workflows/static-analysis/badge.svg?branch=master)](https://github.com/olvlvl/symfony-dependency-injection-proxy/actions?query=workflow%3Astatic-analysis)
[![Code Style](https://github.com/olvlvl/symfony-dependency-injection-proxy/workflows/code-style/badge.svg?branch=master)](https://github.com/olvlvl/symfony-dependency-injection-proxy/actions?query=workflow%3Acode-style)



## Code of Conduct

This project adheres to a [Contributor Code of Conduct](CODE_OF_CONDUCT.md). By participating in
this project and its community, you are expected to uphold this code.



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.



## License

**olvlvl/symfony-dependency-injection-proxy** is released under the [BSD-3-Clause](LICENSE).



[1]: https://symfony.com/doc/current/components/dependency_injection.html
[2]: https://github.com/olvlvl/symfony-dependency-injection-proxy/wiki/Comparison
[3]: https://symfony.com/doc/current/service_container/lazy_services.html
[4]: https://en.wikipedia.org/wiki/Dependency_inversion_principle
