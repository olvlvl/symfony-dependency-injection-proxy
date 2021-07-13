<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace olvlvl\SymfonyDependencyInjectionProxy;

use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

use function array_map;
use function assert;
use function implode;
use function json_encode;

use const PHP_VERSION_ID;

class MethodRenderer
{
    public function __invoke(ReflectionMethod $method, string $getterCode): string
    {
        $signature = $this->renderMethodSignature($method);
        $call = $this->renderCall($method);
        $mayReturn = $this->useReturn($method) ? 'return ' : '';

        return <<<PHPTPL
                $signature
                {
                    {$mayReturn}{$getterCode}->$call;
                }
PHPTPL;
    }

    private function renderMethodSignature(ReflectionMethod $method): string
    {
        $qualifiers = [];

        if ($method->isPublic()) {
            $qualifiers[] = 'public';
        }

        if ($method->isStatic()) {
            $qualifiers[] = 'static';
        }

        $return = '';
        $returnType = $method->getReturnType();

        if ($returnType) {
            $return = ': ' . $this->renderType($returnType);
        }

        $params = [];

        foreach ($method->getParameters() as $parameter) {
            $params[] = $this->renderParameter($parameter);
        }

        return implode(' ', $qualifiers) . " function {$method->getName()}(" . implode(', ', $params) . ")$return";
    }

    /**
     * @throws ReflectionException
     */
    private function renderParameter(ReflectionParameter $parameter): string
    {
        $code = '';
        $type = $parameter->getType();

        if ($type) {
            $code = $this->renderType($type) . ' ';
        }

        $code .= '$' . $parameter->getName();

        if ($parameter->isOptional()) {
            $code .= " = " . json_encode($parameter->getDefaultValue());
        }

        return $code;
    }

    private function renderCall(ReflectionMethod $method): string
    {
        $parameters = implode(
            ', ',
            array_map(
                function (ReflectionParameter $parameter) {
                    return '$' . $parameter->getName();
                },
                $method->getParameters()
            )
        );

        return $method->getName() . "($parameters)";
    }

    private function renderType(ReflectionType $type): string
    {
        if (PHP_VERSION_ID >= 80000 && $type instanceof ReflectionUnionType) {
            return implode('|', array_map(function (ReflectionNamedType $namedType) {
                return $namedType->getName();
            }, $type->getTypes()));
        }

        assert($type instanceof ReflectionNamedType);

        $name = $type->getName();

        return ($name !== 'mixed' && $type->allowsNull() ? '?' : '') . ($type->isBuiltin() ? '' : '\\') . $name;
    }

    private function useReturn(ReflectionMethod $method): bool
    {
        $type = $method->getReturnType();

        if (PHP_VERSION_ID >= 80000 && $type instanceof ReflectionUnionType) {
            return true;
        }

        if ($type instanceof ReflectionNamedType && $type->getName() === 'void') {
            return false;
        }

        return true;
    }
}
