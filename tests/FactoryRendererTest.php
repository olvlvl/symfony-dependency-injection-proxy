<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy;

use olvlvl\SymfonyDependencyInjectionProxy\FactoryRenderer;
use olvlvl\SymfonyDependencyInjectionProxy\MethodRenderer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Serializable;

#[Group('unit')]
final class FactoryRendererTest extends TestCase
{
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
                static fn () => $factoryCode
            ) implements \\$interface
            {
                private \$service;

                public function __construct(private \Closure \$factory)
                {
                }

                codeFor:serialize
                codeFor:unserialize
            };
PHPTPL;
        $this->assertEquals($expected, $stu($interface, $factoryCode));
    }
}
