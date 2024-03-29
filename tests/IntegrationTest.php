<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tests\olvlvl\SymfonyDependencyInjectionProxy;

use olvlvl\SymfonyDependencyInjectionProxy\ProxyDumper;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Reference;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\Buildable;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\BuildableFactory;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\BuildableInterface;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\Sample;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\Sample2;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\SampleInterface;
use tests\olvlvl\SymfonyDependencyInjectionProxy\cases\SampleInterface2;

use function uniqid;

/**
 * @group integration
 */
final class IntegrationTest extends TestCase
{
    /**
     * @dataProvider provideDefinition
     *
     * @param Definition[] $definitions
     */
    public function testCompilation(array $definitions, callable $assert, callable $tweakBuilder = null): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($definitions);

        if ($tweakBuilder) {
            $tweakBuilder($builder);
        }

        $builder->compile();

        $dumper = new PhpDumper($builder);
        $dumper->setProxyDumper(new ProxyDumper());

        $containerClass = 'Container' . uniqid();
        $containerFile = __DIR__ . "/sandbox/$containerClass.php";

        file_put_contents($containerFile, $dumper->dump(['class' => $containerClass]));

        require $containerFile;

        $assert($this, new $containerClass());
    }

    // @phpstan-ignore-next-line
    public static function provideDefinition(): array
    {
        $alias = 'alias-' . uniqid();

        return [

            "service uses a class with one interface" => [
                [
                    $id = uniqid() => (new Definition())
                        ->setClass(Sample::class)
                        ->setLazy(true)
                        ->setPublic(true)
                        ->addArgument($value = uniqid()),
                ],
                function (self $test, ContainerInterface $container) use ($id, $value) {
                    /** @var SampleInterface $service */
                    $service = $container->get($id);
                    $test->assertSame($service, $container->get($id));

                    $test->assertInstanceOf(SampleInterface::class, $service);
                    $test->assertNotInstanceOf(Sample::class, $service);

                    $test->assertSame($value, $service->getValue());
                },
            ],

            "service uses a class with many interfaces" => [
                [
                    $id = uniqid() => (new Definition())
                        ->setClass(Sample2::class)
                        ->setLazy(true)
                        ->setPublic(true)
                        ->addArgument(uniqid())
                        ->addArgument($value2 = uniqid())
                        ->addTag('proxy', ['interface' => SampleInterface2::class]),
                ],
                function (self $test, ContainerInterface $container) use ($id, $value2) {
                    /** @var SampleInterface2 $service */
                    $service = $container->get($id);
                    $test->assertSame($service, $container->get($id));

                    $test->assertInstanceOf(SampleInterface2::class, $service);
                    $test->assertNotInstanceOf(Sample2::class, $service);

                    $test->assertSame($value2, $service->getValue2());
                },
            ],

            "service uses a factory" => [
                [
                    $id = uniqid() => (new Definition())
                        ->setClass(BuildableInterface::class)
                        ->setFactory([ new Reference('factory'), 'build' ])
                        ->setLazy(true)
                        ->setPublic(true),

                    'factory' => (new Definition())
                        ->setClass(BuildableFactory::class)
                        ->addArgument($factoryName = 'factory-' . uniqid()),
                ],
                function (self $test, ContainerInterface $container) use ($id, $factoryName) {
                    /** @var BuildableInterface $service */
                    $service = $container->get($id);
                    $test->assertSame($service, $container->get($id));

                    $test->assertInstanceOf(BuildableInterface::class, $service);
                    $test->assertNotInstanceOf(Buildable::class, $service);

                    $test->assertSame($factoryName, $service->getFactory());
                },
            ],

            "service has an alias" => [
                [
                    $id = uniqid() => (new Definition())
                        ->setClass(Sample::class)
                        ->setLazy(true)
                        ->setPublic(true)
                        ->addArgument($value = uniqid()),
                ],
                function (self $test, ContainerInterface $container) use ($id, $alias, $value) {
                    /** @var SampleInterface $service */
                    $service = $container->get($alias);
                    $test->assertSame($service, $container->get($alias));
                    $test->assertSame($service, $container->get($id));

                    $test->assertInstanceOf(SampleInterface::class, $service);
                    $test->assertNotInstanceOf(Sample::class, $service);

                    $test->assertSame($value, $service->getValue());
                    $test->assertSame($service, $container->get($id));
                },
                function (ContainerBuilder $builder) use ($id, $alias) {
                    $builder->addAliases([ $alias => new Alias($id, true) ]);
                }
            ],

            "service is private but as a public alias" => [
                [
                    $id = uniqid() => (new Definition())
                        ->setClass(Sample::class)
                        ->setLazy(true)
                        ->setPublic(false)
                        ->addArgument($value = uniqid()),
                ],
                function (self $test, ContainerInterface $container) use ($alias, $value) {
                    /** @var SampleInterface $service */
                    $service = $container->get($alias);
                    $test->assertSame($service, $container->get($alias));

                    $test->assertInstanceOf(SampleInterface::class, $service);
                    $test->assertNotInstanceOf(Sample::class, $service);

                    $test->assertSame($value, $service->getValue());
                },
                function (ContainerBuilder $builder) use ($id, $alias) {
                    $builder->addAliases([ $alias => new Alias($id, true) ]);
                },
            ],

        ];
    }
}
