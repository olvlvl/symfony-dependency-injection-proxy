<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace olvlvl\SymfonyDependencyInjectionProxy;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

use function array_map;
use function implode;

use const PHP_VERSION_ID;

class FactoryRenderer
{
    /**
     * @var MethodRenderer
     */
    private $methodRenderer;

    public function __construct(MethodRenderer $methodRenderer)
    {
        $this->methodRenderer = $methodRenderer;
    }

    /**
     * @phpstan-param class-string $interface
     *
     * @throws ReflectionException
     */
    public function __invoke(string $interface, string $factoryCode): string
    {
        $methods = $this->renderMethods(
            (new ReflectionClass($interface))->getMethods(),
            PHP_VERSION_ID >= 70400
                ? '($this->service ??= ($this->factory)())'
                : '($this->service ?: $this->service = ($this->factory)())'
        );

        return <<<PHPTPL
            new class(
                function () {
                    return $factoryCode;
                }
            ) implements \\$interface
            {
                private \$factory, \$service;

                public function __construct(callable \$factory)
                {
                    \$this->factory = \$factory;
                }

$methods
            };
PHPTPL;
    }

    /**
     * @param ReflectionMethod[] $methods
     */
    private function renderMethods(array $methods, string $getterCode): string
    {
        $renderMethod = $this->methodRenderer;

        return implode(
            "\n",
            array_map(
                function (ReflectionMethod $method) use ($renderMethod, $getterCode) {
                    return $renderMethod($method, $getterCode);
                },
                $methods
            )
        );
    }
}
