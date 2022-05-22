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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use ReflectionException;
use ReflectionMethod;
use Serializable;

/**
 * @group unit
 */
final class FactoryRendererTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @throws ReflectionException
     */
    public function testRender(): void
    {
        $interface = Serializable::class;
        $factoryCode = 'someFactoryCode';
        $methodRenderer = $this->prophesize(MethodRenderer::class);
        $methodRenderer->__invoke(
            Argument::type(ReflectionMethod::class),
            '($this->service ??= ($this->factory)())'
        )->will(
            fn(array $args) => '                codeFor:' . $args[0]->getName()
        );

        $stu = new FactoryRenderer($methodRenderer->reveal());
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
