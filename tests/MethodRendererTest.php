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

use const PHP_VERSION_ID;

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

    /**
     * @return array[]
     */
    public function provideRender(): array
    {
        $getterCode = "get()";
        $reflectionFor = function (string $method) {
            return new ReflectionMethod(SampleInterfaceForMethodRenderer70::class, $method);
        };
        $reflectionFor72 = function (string $method) {
            return new ReflectionMethod(SampleInterfaceForMethodRenderer72::class, $method);
        };

        $cases = [

            [
                $reflectionFor('aStaticMethodWithoutParametersOrReturnType'),
                $getterCode,
                <<<PHPTPL
                public static function aStaticMethodWithoutParametersOrReturnType()
                {
                    return {$getterCode}->aStaticMethodWithoutParametersOrReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithoutParametersOrReturnType'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersOrReturnType()
                {
                    return {$getterCode}->aMethodWithoutParametersOrReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithoutParametersButABuiltInReturnType'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButABuiltInReturnType(): array
                {
                    return {$getterCode}->aMethodWithoutParametersButABuiltInReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithoutParametersButABuiltInReturnTypeNullable'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButABuiltInReturnTypeNullable(): ?array
                {
                    return {$getterCode}->aMethodWithoutParametersButABuiltInReturnTypeNullable();
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithoutParametersButANonBuiltInReturnType'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButANonBuiltInReturnType(): \ArrayAccess
                {
                    return {$getterCode}->aMethodWithoutParametersButANonBuiltInReturnType();
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithoutParametersButANonBuiltInReturnTypeNullable'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithoutParametersButANonBuiltInReturnTypeNullable(): ?\ArrayAccess
                {
                    return {$getterCode}->aMethodWithoutParametersButANonBuiltInReturnTypeNullable();
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithParameters1'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithParameters1(\$a, bool \$b, ?int \$c, \$d = null)
                {
                    return {$getterCode}->aMethodWithParameters1(\$a, \$b, \$c, \$d);
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithParameters2'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithParameters2(\ArrayAccess \$a, ?\ArrayAccess \$b, ?\ArrayAccess \$c = null)
                {
                    return {$getterCode}->aMethodWithParameters2(\$a, \$b, \$c);
                }
PHPTPL
            ],

            [
                $reflectionFor('aMethodWithParameters3'),
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

        ];

        if (PHP_VERSION_ID >= 80000) {
            $reflectionFor80 = function (string $method) {
                return new ReflectionMethod(SampleInterfaceForMethodRenderer80::class, $method);
            };

            $cases["aMethodWithMixed"] = [
                $reflectionFor80('aMethodWithMixed'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithMixed(mixed \$a): mixed
                {
                    return {$getterCode}->aMethodWithMixed(\$a);
                }
PHPTPL
            ];

            $cases["aMethodWithUnionTypes"] = [
                $reflectionFor80('aMethodWithUnionTypes'),
                $getterCode,
                <<<PHPTPL
                public function aMethodWithUnionTypes(string|int|null \$a): string|int|null
                {
                    return {$getterCode}->aMethodWithUnionTypes(\$a);
                }
PHPTPL
            ];
        }

        return $cases;
    }
}
