<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\olvlvl\SymfonyDependencyInjectionProxy;

use olvlvl\SymfonyDependencyInjectionProxy\FactoryRenderer;
use olvlvl\SymfonyDependencyInjectionProxy\MethodRenderer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionMethod;
use Serializable;

/**
 * @group unit
 */
final class FactoryRendererTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testRender(): void
    {
        $interface = Serializable::class;
        $factoryCode = 'someFactoryCode';
        $methodRenderer = $this->createMock(MethodRenderer::class);
        $methodRenderer
            ->method('__invoke')
            ->with(
                Assert::isInstanceOf(ReflectionMethod::class),
                '($this->service ??= ($this->factory)())'
            )->willReturnCallback(
                fn(ReflectionMethod $method): string => '                codeFor:' . $method->getName()
            );

        $stu = new FactoryRenderer($methodRenderer);
        $expected = <<<PHPTPL
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

                codeFor:serialize
                codeFor:unserialize
            };
PHPTPL;
        $this->assertEquals($expected, $stu($interface, $factoryCode));
    }
}
