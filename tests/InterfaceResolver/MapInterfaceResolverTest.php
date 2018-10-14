<?php

namespace tests\olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;

use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver;
use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver\MapInterfaceResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @group unit
 */
class MapInterfaceResolverTest extends TestCase
{
    /**
     * @dataProvider provideResolveInterface
     *
     * @throws \Exception
     */
    public function testResolveInterface(
        string $class,
        string $expected,
        callable $makeInterfaceResolver,
        array $map
    ) {
        $stu = new MapInterfaceResolver($makeInterfaceResolver(), $map);
        $this->assertSame($expected, $stu->resolveInterface($class));
    }

    public function provideResolveInterface(): array
    {
        $class = 'aClass';
        $interface = 'anInterface';

        return [

            "should resolve the interface using the map" => [
                $class,
                $interface,
                function () {
                    $stu = $this->prophesize(InterfaceResolver::class);
                    $stu->resolveInterface(Argument::any())->shouldNotBeCalled();
                    return $stu->reveal();
                },
                [ $class => $interface ]

            ],

            "should resolve the interface using the next resolver" => [
                $class,
                $interface,
                function () use ($class, $interface) {
                    $stu = $this->prophesize(InterfaceResolver::class);
                    $stu->resolveInterface($class)->willReturn($interface);
                    return $stu->reveal();
                },
                [ 'anotherClass' => 'anotherInterface' ]

            ],

        ];
    }
}
