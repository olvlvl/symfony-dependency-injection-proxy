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

use ArrayAccess;
use ArrayIterator;
use ArrayObject;
use Exception;
use InvalidArgumentException;
use olvlvl\SymfonyDependencyInjectionProxy\FactoryRenderer;
use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;
use olvlvl\SymfonyDependencyInjectionProxy\ProxyDumper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @group unit
 */
final class ProxyDumperTest extends TestCase
{
    /**
     * @dataProvider provideIsProxyCandidate
     */
    public function testIsProxyCandidate(Definition $definition, bool $expected): void
    {
        $stu = new ProxyDumper(
            $this->prophesize(InterfaceResolver::class)->reveal(),
            $this->prophesize(FactoryRenderer::class)->reveal()
        );

        $this->assertSame($expected, $stu->isProxyCandidate($definition));
    }

    /**
     * @return array[]
     */
    public function provideIsProxyCandidate(): array
    {
        $factory = 'aFactory';
        $class = ArrayObject::class;
        $interface = ArrayAccess::class;

        return [

            [ (new Definition())->setLazy(false), false ],
            [ (new Definition())->setLazy(false)->setFactory($factory), false ],
            [ (new Definition())->setLazy(false)->setClass($class), false ],
            [ (new Definition())->setLazy(false)->setFactory($factory)->setClass($class), false ],
            [ (new Definition())->setLazy(true), false ],
            [ (new Definition())->setLazy(true)->setFactory($factory), true ],
            [ (new Definition())->setLazy(true)->setClass($class), true ],
            [ (new Definition())->setLazy(true)->setClass($interface), false ],
            [ (new Definition())->setLazy(true)->setFactory($factory)->setClass($class), true ],
            [ (new Definition())->setLazy(true)->setFactory($factory)->setClass($interface), true ],

        ];
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldFailIfFactoryCodeIsEmpty(): void
    {
        $stu = new ProxyDumper(
            $this->prophesize(InterfaceResolver::class)->reveal(),
            $this->prophesize(FactoryRenderer::class)->reveal()
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing factory code to construct the service `aServiceId`.");
        $stu->getProxyFactoryCode(new Definition(), 'aServiceId', '');
    }

    /**
     * @throws Exception
     * @dataProvider provideGetProxyFactoryCode
     */
    public function testGetProxyFactoryCode(string $id, bool $private, bool $shared, string $expectedStore): void
    {
        $definition = (new Definition())
            ->setClass($class = ArrayIterator::class)
            ->setPrivate($private)
            ->setShared($shared);
        $interfaceResolver = $this->prophesize(InterfaceResolver::class);
        $interfaceResolver->resolveInterface($class)
            ->willReturn($interface = ArrayAccess::class);
        $factoryRenderer = $this->prophesize(FactoryRenderer::class);
        $factoryRenderer->__invoke($interface, $factoryCode = 'someFactoryCode')
            ->willReturn($proxyFactoryCode = 'someProxyFactoryCode');

        $stu = new ProxyDumper(
            $interfaceResolver->reveal(),
            $factoryRenderer->reveal()
        );

        $expected = <<<PHPTPL
        if (\$lazyLoad) {
            return {$expectedStore}$proxyFactoryCode
        }


PHPTPL;

        $this->assertEquals($expected, $stu->getProxyFactoryCode($definition, $id, $factoryCode));
    }

    /**
     * @return array[]
     */
    public function provideGetProxyFactoryCode(): array
    {
        $id = 'aServiceId';

        return [

            [ $id, false, true, "\$this->services['$id'] = " ],
            [ $id, true, true, "\$this->privates['$id'] = " ],
            [ $id, false, false, "" ],
            [ $id, true, false, "" ],

        ];
    }

    /**
     * @see https://github.com/symfony/symfony/issues/28852
     */
    public function testGetProxyCode(): void
    {
        $stu = new ProxyDumper(
            $this->prophesize(InterfaceResolver::class)->reveal(),
            $this->prophesize(FactoryRenderer::class)->reveal()
        );

        $proxyCode = $stu->getProxyCode(new Definition());

        $this->assertEmpty($proxyCode);
    }
}
