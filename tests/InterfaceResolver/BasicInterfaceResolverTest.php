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
use Exception;
use LogicException;
use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver\BasicInterfaceResolver;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class BasicInterfaceResolverTest extends TestCase
{
    /**
     * @test
     * @throws Exception
     */
    public function shouldFailIfClassImplementsManyInterfaces(): void
    {
        $stu = new BasicInterfaceResolver();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches(
            "/Don't know which interface to choose from for ArrayIterator: Iterator,/"
        );
        $stu->resolveInterface(ArrayIterator::class);
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldFailIfClassDoesNotExist(): void
    {
        $stu = new BasicInterfaceResolver();
        /** @phpstan-var class-string $undefinedClass */
        $undefinedClass = 'anUndefinedClass';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Unable to determine the interface to implement for anUndefinedClass.");
        $stu->resolveInterface($undefinedClass);
    }

    /**
     * @dataProvider provideResolveInterface
     *
     * @phpstan-param class-string $class
     *
     * @throws Exception
     */
    public function testResolveInterface(string $class, string $expected): void
    {
        $stu = new BasicInterfaceResolver();
        $this->assertSame($expected, $stu->resolveInterface($class));
    }

    /**
     * @return array[]
     */
    public function provideResolveInterface(): array
    {
        return [

            "given an interface, should return the same interface" => [
                DateTimeInterface::class,
                DateTimeInterface::class,
            ],

            "given an class with a single interface, should return its interface" => [
                DateTimeImmutable::class,
                DateTimeInterface::class,
            ],

        ];
    }
}
