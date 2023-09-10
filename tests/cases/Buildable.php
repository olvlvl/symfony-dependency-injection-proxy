<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\cases;

class Buildable implements BuildableInterface
{
    /**
     * @var string
     */
    private $factory;

    public function __construct(string $factory = 'none')
    {
        $this->factory = $factory;
    }

    public function getFactory(): string
    {
        return $this->factory;
    }
}
