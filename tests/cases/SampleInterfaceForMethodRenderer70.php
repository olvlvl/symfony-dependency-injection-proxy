<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\cases;

use ArrayAccess;

interface SampleInterfaceForMethodRenderer70
{
    public const A_CONSTANT = 'aConstantValue';

    public static function aStaticMethodWithoutParametersOrReturnType();

    public function aMethodWithoutParametersOrReturnType();

    public function aMethodWithoutParametersButABuiltInReturnType(): array;

    public function aMethodWithoutParametersButABuiltInReturnTypeNullable(): ?array;

    public function aMethodWithoutParametersButANonBuiltInReturnType(): ArrayAccess;

    public function aMethodWithoutParametersButANonBuiltInReturnTypeNullable(): ?ArrayAccess;

    public function aMethodWithParameters1($a, bool $b, ?int $c, $d = null);

    public function aMethodWithParameters2(ArrayAccess $a, ?ArrayAccess $b, ?ArrayAccess $c = null);

    public function aMethodWithParameters3($a = 123, $b = "abc", $c = self::A_CONSTANT);
}
