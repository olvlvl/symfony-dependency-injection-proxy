<?php

/*
 * This file is part of the olvlvl/symfony-dependency-injection-proxy package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace olvlvl\SymfonyDependencyInjectionProxy;

use InvalidArgumentException;
use olvlvl\SymfonyDependencyInjectionProxy\InterfaceResolver\BasicInterfaceResolver;
use ReflectionException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\DumperInterface;
use Throwable;

use function class_exists;
use function ltrim;
use function sprintf;

final readonly class ProxyDumper implements DumperInterface
{
    public function __construct(
        private InterfaceResolver $interfaceResolver = new BasicInterfaceResolver(),
        private FactoryRenderer $factoryRenderer = new FactoryRenderer(new MethodRenderer()),
    ) {
    }

    /**
     * @inheritdoc
     */
    public function isProxyCandidate(Definition $definition, bool &$asGhostObject = null, string $id = null): bool
    {
        $class = $definition->getClass();

        return $definition->isLazy() && ($definition->getFactory() || ($class && class_exists($class)));
    }

    /**
     * @inheritdoc
     * @throws Throwable
     */
    public function getProxyFactoryCode(Definition $definition, string $id, string $factoryCode): string
    {
        if (!$factoryCode) {
            throw new InvalidArgumentException("Missing factory code to construct the service `$id`.");
        }

        $store = '';

        if ($definition->isShared()) {
            $store = sprintf(
                '$container->%s[\'%s\'] = ',
                $definition->isPublic() && !$definition->isPrivate() ? 'services' : 'privates',
                $id
            );
        }

        $interface = $this->findInterface($definition);
        $proxy = ltrim($this->renderFactory($interface, $factoryCode));

        return <<<PHPTPL
        if (\$lazyLoad) {
            return $store$proxy
        }


PHPTPL;
    }

    /**
     * @inheritdoc
     */
    public function getProxyCode(Definition $definition, string $id = null): string
    {
        return '';
    }

    /**
     * @return class-string
     *
     * @throws Throwable
     */
    private function findInterface(Definition $definition): string
    {
        $interface = $this->resolveInterfaceFromTags($definition);

        if ($interface) {
            return $interface;
        }

        $class = $definition->getClass();

        if (!$class) {
            throw new LogicException("Unable to resolve interface, class is missing.");
        }

        /** @var class-string $class */

        return $this->interfaceResolver->resolveInterface($class);
    }

    /**
     * @return class-string|null
     */
    private function resolveInterfaceFromTags(Definition $definition): ?string
    {
        $proxy = $definition->getTag('proxy');

        return $proxy[0]['interface'] ?? null;
    }

    /**
     * @param class-string $interface
     *
     * @throws ReflectionException
     */
    private function renderFactory(string $interface, string $factoryCode): string
    {
        return ($this->factoryRenderer)($interface, $factoryCode);
    }
}
