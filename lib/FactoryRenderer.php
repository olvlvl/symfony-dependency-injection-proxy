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

class FactoryRenderer
{
    public function __construct(
        private MethodRenderer $methodRenderer
    ) {
    }

    /**
     * @phpstan-param class-string $interface
     *
     * @throws ReflectionException
     */
    public function __invoke(string $interface, string $factoryCode): string
    {
        $methods = $this->renderMethods((new ReflectionClass($interface))->getMethods());

        return <<<PHPTPL
            new class(
                function () {
                    return $factoryCode;
                }
            ) implements \\$interface
            {
                private \$service;

                public function __construct(
                    private \Closure \$factory
                ) {
                }

$methods
            };
PHPTPL;
    }

    /**
     * @param ReflectionMethod[] $methods
     */
    private function renderMethods(array $methods): string
    {
        $renderMethod = $this->methodRenderer;

        return implode(
            "\n",
            array_map(
                fn(ReflectionMethod $method) => $renderMethod(
                    $method,
                    '($this->service ??= ($this->factory)())'
                ),
                $methods
            )
        );
    }
}
