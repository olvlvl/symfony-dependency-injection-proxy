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
use const PHP_VERSION_ID;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\SampleInterfaceForMethodRenderer70;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\SampleInterfaceForMethodRenderer72;

/**
 * @group unit
 */
class MethodRendererTest extends TestCase
{
    /**
     * @dataProvider provideRender
     */
    public function testRender(ReflectionMethod $method, string $getterCode, string $expected)
    {
        $stu = new MethodRenderer();

        $this->assertEquals($expected, $stu($method, $getterCode));
    }

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
                <<<PHP
                public static function aStaticMethodWithoutParametersOrReturnType()
                {
                    return {$getterCode}->aStaticMethodWithoutParametersOrReturnType();
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithoutParametersOrReturnType'),
                $getterCode,
                <<<PHP
                public function aMethodWithoutParametersOrReturnType()
                {
                    return {$getterCode}->aMethodWithoutParametersOrReturnType();
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithoutParametersButABuiltInReturnType'),
                $getterCode,
                <<<PHP
                public function aMethodWithoutParametersButABuiltInReturnType(): array
                {
                    return {$getterCode}->aMethodWithoutParametersButABuiltInReturnType();
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithoutParametersButABuiltInReturnTypeNullable'),
                $getterCode,
                <<<PHP
                public function aMethodWithoutParametersButABuiltInReturnTypeNullable(): ?array
                {
                    return {$getterCode}->aMethodWithoutParametersButABuiltInReturnTypeNullable();
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithoutParametersButANonBuiltInReturnType'),
                $getterCode,
                <<<PHP
                public function aMethodWithoutParametersButANonBuiltInReturnType(): \ArrayAccess
                {
                    return {$getterCode}->aMethodWithoutParametersButANonBuiltInReturnType();
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithoutParametersButANonBuiltInReturnTypeNullable'),
                $getterCode,
                <<<PHP
                public function aMethodWithoutParametersButANonBuiltInReturnTypeNullable(): ?\ArrayAccess
                {
                    return {$getterCode}->aMethodWithoutParametersButANonBuiltInReturnTypeNullable();
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithParameters1'),
                $getterCode,
                <<<PHP
                public function aMethodWithParameters1(\$a, bool \$b, ?int \$c, \$d = null)
                {
                    return {$getterCode}->aMethodWithParameters1(\$a, \$b, \$c, \$d);
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithParameters2'),
                $getterCode,
                <<<PHP
                public function aMethodWithParameters2(\ArrayAccess \$a, ?\ArrayAccess \$b, ?\ArrayAccess \$c = null)
                {
                    return {$getterCode}->aMethodWithParameters2(\$a, \$b, \$c);
                }
PHP
            ],

            [
                $reflectionFor('aMethodWithParameters3'),
                $getterCode,
                <<<PHP
                public function aMethodWithParameters3(\$a = 123, \$b = "abc", \$c = "aConstantValue")
                {
                    return {$getterCode}->aMethodWithParameters3(\$a, \$b, \$c);
                }
PHP
            ],

        ];

        if (PHP_VERSION_ID >= 72000) {
            $cases[] = [

                $reflectionFor72('aMethodWithReturnTypeVoid'),
                $getterCode,
                <<<PHP
                public function aMethodWithReturnTypeVoid(\$a): void
                {
                    {$getterCode}->aMethodWithReturnTypeVoid(\$a);
                }
PHP
            ];
        }

        return $cases;
    }
}
