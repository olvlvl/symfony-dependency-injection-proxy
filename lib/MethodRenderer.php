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

use ReflectionMethod;
use ReflectionParameter;
use ReflectionType;

use function implode;
use function json_encode;

class MethodRenderer
{
    public function __invoke(ReflectionMethod $method, string $getterCode): string
    {
        $signature = $this->renderMethodSignature($method);
        $call = $this->renderCall($method);
        $mayReturn = ($method->hasReturnType() && $method->getReturnType()->getName() === 'void') ? '' : 'return ';

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

        if ($method->hasReturnType()) {
            $type = $method->getReturnType();
            $return = ': ' . $this->renderType($type);
        }

        $params = [];

        foreach ($method->getParameters() as $parameter) {
            $params[] = $this->renderParameter($parameter);
        }

        return implode(' ', $qualifiers) . " function {$method->getName()}(" . implode(', ', $params) . ")$return";
    }

    private function renderParameter(ReflectionParameter $parameter): string
    {
        $code = '';

        if ($parameter->hasType()) {
            $code = $this->renderType($parameter->getType()) . ' ';
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
        return ($type->allowsNull() ? '?' : '') . ($type->isBuiltin() ? '' : '\\') . $type->getName();
    }
}
