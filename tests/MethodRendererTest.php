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

use olvlvl\SymfonyDependencyInjectionProxy\MethodRenderer;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\SampleInterfaceForMethodRenderer70;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\SampleInterfaceForMethodRenderer72;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\SampleInterfaceForMethodRenderer80;

/**
 * @group unit
 */
final class MethodRendererTest extends TestCase
{
    /**
     * @dataProvider provideRender
     */
    public function testRender(ReflectionMethod $method, string $getterCode, string $expected): void
    {
        $stu = new MethodRenderer();

        $this->assertEquals($expected, $stu($method, $getterCode));
    }

    // @phpstan-ignore-next-line
    public function provideRender(): array
    {
        $getterCode = "get()";
        $reflectionFor70 = fn(string $method) => new ReflectionMethod(
            SampleInterfaceForMethodRenderer70::class,
            $method
        );
        $reflectionFor72 = fn(string $method) => new ReflectionMethod(
            SampleInterfaceForMethodRenderer72::class,
            $method
        );
        $reflectionFor80 = fn(string $method) => new ReflectionMethod(
            SampleInterfaceForMethodRenderer80::class,
            $method
        );

        $cases = [

            [
                $reflectionFor70('aStaticMethodWithoutParametersOrReturnType'),
                $getterCode,
                <<<PHPTPL
                public static function aStaticMethodWithoutParametersOrReturnType()
                {
                    return {$getterCode}->aStaticMethodWithoutParametersOrReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithoutParametersOrReturnType'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersOrReturnType()
                {
                    return {$getterCode}->aMethodWithoutParametersOrReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithoutParametersButABuiltInReturnType'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButABuiltInReturnType(): array
                {
                    return {$getterCode}->aMethodWithoutParametersButABuiltInReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithoutParametersButABuiltInReturnTypeNullable'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButABuiltInReturnTypeNullable(): ?array
                {
                    return {$getterCode}->aMethodWithoutParametersButABuiltInReturnTypeNullable();
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithoutParametersButANonBuiltInReturnType'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButANonBuiltInReturnType(): \ArrayAccess
                {
                    return {$getterCode}->aMethodWithoutParametersButANonBuiltInReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithoutParametersButANonBuiltInReturnTypeNullable'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButANonBuiltInReturnTypeNullable(): ?\ArrayAccess
                {
                    return {$getterCode}->aMethodWithoutParametersButANonBuiltInReturnTypeNullable();
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithParameters1'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithParameters1(\$a, bool \$b, ?int \$c, \$d = null)
                {
                    return {$getterCode}->aMethodWithParameters1(\$a, \$b, \$c, \$d);
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithParameters2'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithParameters2(\ArrayAccess \$a, ?\ArrayAccess \$b, ?\ArrayAccess \$c = null)
                {
                    return {$getterCode}->aMethodWithParameters2(\$a, \$b, \$c);
                }
PHPTPL
            ],

            [
                $reflectionFor70('aMethodWithParameters3'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithParameters3(\$a = 123, \$b = "abc", \$c = "aConstantValue")
                {
                    return {$getterCode}->aMethodWithParameters3(\$a, \$b, \$c);
                }
PHPTPL
            ],

            [

                $reflectionFor72('aMethodWithReturnTypeVoid'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithReturnTypeVoid(\$a): void
                {
                    {$getterCode}->aMethodWithReturnTypeVoid(\$a);
                }
PHPTPL
            ],

            [

                $reflectionFor72('aMethodWithReturnTypeObject'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithReturnTypeObject(\$a): object
                {
                    return {$getterCode}->aMethodWithReturnTypeObject(\$a);
                }
PHPTPL
            ],

            "a method with mixed" => [
                $reflectionFor80('aMethodWithMixed'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithMixed(mixed \$a): mixed
                {
                    return {$getterCode}->aMethodWithMixed(\$a);
                }
PHPTPL
            ],

            "a method with union types" => [
                $reflectionFor80('aMethodWithUnionTypes'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithUnionTypes(string|int|null \$a): string|int|null
                {
                    return {$getterCode}->aMethodWithUnionTypes(\$a);
                }
PHPTPL
            ],

        ];

        return $cases;
    }
}
