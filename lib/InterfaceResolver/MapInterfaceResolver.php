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

use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;

final class MapInterfaceResolver implements InterfaceResolver
{
    /**
     * @var InterfaceResolver
     */
    private $next;

    /**
     * @var array
     */
    private $map;

    public function __construct(InterfaceResolver $next, array $map)
    {
        $this->next = $next;
        $this->map = $map;
    }

    /**
     * @inheritdoc
     */
    public function resolveInterface(string $class): string
    {
        if (isset($this->map[$class])) {
            return $this->map[$class];
        }

        return $this->next->resolveInterface($class);
    }
}
