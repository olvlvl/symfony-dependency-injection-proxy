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

use Throwable;

interface InterfaceResolver
{
    /**
     * Given a class, resolve the interface to use to create its proxy.
     *
     * @param class-string $class
     *
     * @return class-string
     *
     * @throws Throwable if the interface cannot be resolved.
     */
    public function resolveInterface(string $class): string;
}
