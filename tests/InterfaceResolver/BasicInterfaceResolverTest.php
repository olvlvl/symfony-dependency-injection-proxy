<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;

use ArrayIterator;
use DateTimeImmutable;
use DateTimeInterface;
use LogicException;
use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver\BasicInterfaceResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

#[Group('unit')]
final class BasicInterfaceResolverTest extends TestCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function shouldFailIfClassImplementsManyInterfaces(): void
    {
        $stu = new BasicInterfaceResolver();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches(
            "/Don't know which interface to choose from for ArrayIterator:.*Iterator/"
        );
        $stu->resolveInterface(ArrayIterator::class);
    }

    /**
     * @throws Throwable
     */
    #[Test]
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
     * @param class-string $class
     *
     * @throws Throwable
     */
    #[DataProvider('provideResolveInterface')]
    public function testResolveInterface(string $class, string $expected): void
    {
        $stu = new BasicInterfaceResolver();
        $this->assertSame($expected, $stu->resolveInterface($class));
    }

    // @phpstan-ignore-next-line
    public static function provideResolveInterface(): array
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
