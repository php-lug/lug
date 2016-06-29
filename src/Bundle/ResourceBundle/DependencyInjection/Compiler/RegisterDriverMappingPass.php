<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\Bundle\MongoDBBundle\DependencyInjection\Compiler\DoctrineMongoDBMappingsPass;
use Lug\Bundle\ResourceBundle\Util\ClassUtils;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterDriverMappingPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach (array_keys($container->findTaggedServiceIds('lug.resource')) as $id) {
            $this->getCompilerPass($container->getDefinition($id))->process($container);
        }
    }

    /**
     * @param Definition $definition
     *
     * @return CompilerPassInterface
     */
    private function getCompilerPass(Definition $definition)
    {
        $driver = $this->getResourceDriver($definition);

        if ($driver === null) {
            return;
        }

        $class = $this->getCompilerPassClass($driver);
        $method = $this->getCompilerPassMethod($this->getResourceDriverMappingFormat($definition));
        $path = $this->getResourceDriverMappingPath($definition);
        $model = $this->getResourceModel($definition);

        return $class::$method([$path => ClassUtils::getRealNamespace($model)], []);
    }

    /**
     * @param Definition $definition
     *
     * @return string|null
     */
    private function getResourceModel(Definition $definition)
    {
        return $definition->getArgument(2);
    }

    /**
     * @param Definition $definition
     *
     * @return string|null
     */
    private function getResourceDriver(Definition $definition)
    {
        return $this->getResourceMetadata($definition, 'setDriver');
    }

    /**
     * @param Definition $definition
     *
     * @return string|null
     */
    private function getResourceDriverMappingPath(Definition $definition)
    {
        return $this->getResourceMetadata($definition, 'setDriverMappingPath');
    }

    /**
     * @param Definition $definition
     *
     * @return string|null
     */
    private function getResourceDriverMappingFormat(Definition $definition)
    {
        return $this->getResourceMetadata($definition, 'setDriverMappingFormat');
    }

    /**
     * @param Definition $definition
     * @param string     $method
     *
     * @return string
     */
    private function getResourceMetadata(Definition $definition, $method)
    {
        foreach ($definition->getMethodCalls() as $methodCall) {
            if ($methodCall[0] === $method) {
                return $methodCall[1][0];
            }
        }
    }

    /**
     * @param string $driver
     *
     * @return string
     */
    private function getCompilerPassClass($driver)
    {
        return $driver === ResourceInterface::DRIVER_DOCTRINE_ORM
            ? DoctrineOrmMappingsPass::class
            : DoctrineMongoDBMappingsPass::class;
    }

    /**
     * @param string $format
     *
     * @return string
     */
    private function getCompilerPassMethod($format)
    {
        return 'create'.ucfirst($format).'MappingDriver';
    }
}
