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

interface SampleInterfaceForMethodRenderer72
{
    public function aMethodWithReturnTypeVoid($a): void;

    public function aMethodWithReturnTypeObject($a): object;
}
