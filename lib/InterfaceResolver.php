<?php

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
