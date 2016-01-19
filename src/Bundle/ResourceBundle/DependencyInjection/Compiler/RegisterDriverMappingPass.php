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
     * @param Definition $resource
     *
     * @return CompilerPassInterface
     */
    private function getCompilerPass(Definition $resource)
    {
        $driver = $resource->getArgument(1);
        $path = $resource->getArgument(3);
        $format = $resource->getArgument(4);
        $model = $resource->getArgument(6);

        $class = $this->getCompilerPassClass($driver);
        $method = $this->getCompilerPassMethod($format);

        return $class::$method([$path => ClassUtils::getRealNamespace($model)], []);
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
