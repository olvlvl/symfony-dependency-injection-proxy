<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;

use LogicException;
use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;

use function class_exists;
use function class_implements;
use function count;
use function implode;
use function interface_exists;
use function reset;

final class BasicInterfaceResolver implements InterfaceResolver
{
    /**
     * @inheritdoc
     */
    public function resolveInterface(string $class): string
    {
        if (interface_exists($class)) {
            return $class;
        }

        if (class_exists($class)) {
            $interfaces = class_implements($class);

            if (count($interfaces) > 1) {
                $interfaces = implode(', ', $interfaces);
                throw new LogicException("Don't know which interface to choose from for $class: $interfaces.");
            }

            return reset($interfaces);
        }

        throw new LogicException("Unable to determine the interface to implement for $class.");
    }
}
