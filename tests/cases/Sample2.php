<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\cases;

class Sample2 implements SampleInterface, SampleInterface2
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $value2;

    public function __construct(string $value, string $value2)
    {
        $this->value = $value;
        $this->value2 = $value2;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getValue2(): string
    {
        return $this->value2;
    }
}
