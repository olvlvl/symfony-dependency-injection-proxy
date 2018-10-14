<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\cases;

class BuildableFactory
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function build()
    {
        return new Buildable($this->name);
    }
}
