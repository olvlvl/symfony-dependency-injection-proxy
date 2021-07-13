<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\cases;

interface SampleInterfaceForMethodRenderer80
{
    public function aMethodWithMixed(mixed $a): mixed;

    public function aMethodWithUnionTypes(int|string|null $a): int|string|null;
}
