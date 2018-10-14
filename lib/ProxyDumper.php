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
use function method_exists;
use function sprintf;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\LazyProxy\PhpDumper\DumperInterface;
use function class_exists;
use function ltrim;

final class ProxyDumper implements DumperInterface
{
    /**
     * @var InterfaceResolver
     */
    private $interfaceResolver;

    /**
     * @var FactoryRenderer
     */
    private $factoryRenderer;

    public function __construct(InterfaceResolver $interfaceResolver, FactoryRenderer $factoryRenderer)
    {
        $this->interfaceResolver = $interfaceResolver;
        $this->factoryRenderer = $factoryRenderer;
    }

    /**
     * @inheritdoc
     */
    public function isProxyCandidate(Definition $definition)
    {
        return $definition->isLazy() && ($definition->getFactory() || class_exists($definition->getClass()));
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function getProxyFactoryCode(Definition $definition, $id, $factoryCode)
    {
        if (!$factoryCode) {
            throw new InvalidArgumentException("Missing factory code to construct the service `$id`.");
        }

        $store = '';

        if ($definition->isShared()) {
            $store = sprintf(
                '$this->%s[\'%s\'] = ',
                $definition->isPublic() && !$definition->isPrivate() ? 'services' : 'privates',
                $id
            );
        }

        $interface = $this->findInterface($definition);
        $proxy = ltrim($this->renderFactory($interface, $factoryCode));

        return <<<PHP
        if (\$lazyLoad) {
            return {$store}$proxy
        }


PHP;
    }

    /**
     * @inheritdoc
     */
    public function getProxyCode(Definition $definition)
    {
        return '// nothing to do';
    }

    /**
     * @throws \Exception
     */
    private function findInterface(Definition $definition): string
    {
        return $this->interfaceResolver->resolveInterface($definition->getClass());
    }

    private function renderFactory(string $interface, string $factoryCode): string
    {
        return ($this->factoryRenderer)($interface, $factoryCode);
    }
}
