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
use ReflectionMethod;
use function array_map;

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
     * @throws \ReflectionException
     */
    public function __invoke(string $interface, string $factoryCode): string
    {
        $methods = $this->renderMethods(
            (new ReflectionClass($interface))->getMethods(),
            '($this->service ?: $this->service = ($this->factory)())'
        );

        return <<<PHP
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
PHP;
    }

    /**
     * @param ReflectionMethod[] $methods
     */
    private function renderMethods(array $methods, string $getterCode)
    {
        $renderMethod = $this->methodRenderer;

        return implode("\n", array_map(function (ReflectionMethod $method) use ($renderMethod, $getterCode) {
            return $renderMethod($method, $getterCode);
        }, $methods));
    }
}
