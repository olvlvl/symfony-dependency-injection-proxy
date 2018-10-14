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
use ReflectionMethod;
use Serializable;

/**
 * @group unit
 */
class FactoryRendererTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testRender()
    {
        $interface = Serializable::class;
        $factoryCode = 'someFactoryCode';
        $methodRenderer = $this->prophesize(MethodRenderer::class);
        $methodRenderer->__invoke(
            Argument::type(ReflectionMethod::class),
            '($this->service ?: $this->service = ($this->factory)())'
        )->will(function (array $args) {
            return '                codeFor:' . $args[0]->getName();
        });

        $stu = new FactoryRenderer($methodRenderer->reveal());
        $expected = <<<PHP
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

                codeFor:serialize
                codeFor:unserialize
            };
PHP;
        $this->assertEquals($expected, $stu($interface, $factoryCode));
    }
}
