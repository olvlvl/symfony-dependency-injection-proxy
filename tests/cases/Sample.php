<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\cases;

class Sample implements SampleInterface
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
