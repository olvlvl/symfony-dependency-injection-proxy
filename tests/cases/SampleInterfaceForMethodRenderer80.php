<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\cases;

interface SampleInterfaceForMethodRenderer80
{
    public function aMethodWithMixed(mixed $a): mixed;

    public function aMethodWithUnionTypes(int|string|null $a): int|string|null;
}
