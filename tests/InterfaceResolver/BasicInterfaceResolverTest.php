<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;

use ArrayIterator;
use DateTimeImmutable;
use DateTimeInterface;
use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver\BasicInterfaceResolver;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class BasicInterfaceResolverTest extends TestCase
{
    /**
     * @test
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /Don't know which interface to choose from for ArrayIterator: Iterator,/
     * @throws \Exception
     */
    public function shouldFailIfClassImplementsManyInterfaces()
    {
        $stu = new BasicInterfaceResolver();
        $stu->resolveInterface(ArrayIterator::class);
    }

    /**
     * @test
     * @expectedException \LogicException
     * @expectedExceptionMessage Unable to determine the interface to implement for anUndefinedClass.
     * @throws \Exception
     */
    public function shouldFailIfClassDoesNotExist()
    {
        $stu = new BasicInterfaceResolver();
        $stu->resolveInterface('anUndefinedClass');
    }

    /**
     * @dataProvider provideResolveInterface
     *
     * @throws \Exception
     */
    public function testResolveInterface(string $class, string $expected)
    {
        $stu = new BasicInterfaceResolver();
        $this->assertSame($expected, $stu->resolveInterface($class));
    }

    public function provideResolveInterface(): array
    {
        return [

            "given an interface, should return the same interface" => [
                DateTimeInterface::class,
                DateTimeInterface::class
            ],

            "given an class with a single interface, should return its interface" => [
                DateTimeImmutable::class,
                DateTimeInterface::class
            ],

        ];
    }
}
