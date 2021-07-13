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

use Exception;

interface InterfaceResolver
{
    /**
     * Given a class, resolve the interface to use to create its proxy.
     *
     * @phpstan-param class-string $class
     *
     * @phpstan-return class-string
     * @throws Exception if the interface cannot be resolved.
     */
    public function resolveInterface(string $class): string;
}
