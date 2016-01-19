<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\DependencyInjection\Extension;

use Lug\Bundle\ResourceBundle\ResourceBundleInterface;
use Lug\Component\Resource\Controller\ControllerInterface;
use Lug\Component\Resource\Domain\DomainManagerInterface;
use Lug\Component\Resource\Factory\FactoryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceConfiguration implements ConfigurationInterface
{
    /**
     * @var ResourceBundleInterface
     */
    private $bundle;

    /**
     * @param ResourceBundleInterface $bundle
     */
    public function __construct(ResourceBundleInterface $bundle)
    {
        $this->bundle = $bundle;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = $this->createTreeBuilder();
        $children = $treeBuilder->root($this->bundle->getAlias())->children();

        $this->configureBundle($children);
        $children->append($this->createResourceNodes());

        return $treeBuilder;
    }

    /**
     * @param NodeBuilder $builder
     */
    protected function configureBundle(NodeBuilder $builder)
    {
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createResourceNodes()
    {
        $resourcesNode = $this->createNode('resources')->addDefaultsIfNotSet();
        $childrenNode = $resourcesNode->children();

        foreach ($this->bundle->getResources() as $resource) {
            $childrenNode->append($this->createResourceNode($resource));
        }

        return $resourcesNode;
    }

    /**
     * @param ResourceInterface $resource
     * @param string|null       $name
     *
     * @return ArrayNodeDefinition
     */
    private function createResourceNode(ResourceInterface $resource, $name = null)
    {
        $resourceNode = $this->createNode($name ?: $resource->getName())->addDefaultsIfNotSet();
        $childrenNode = $resourceNode->children()
            ->append($this->createDriverNode($resource))
            ->append($this->createClassNode('model', $resource->getModel(), $resource->getInterfaces()))
            ->append($this->createClassNode('controller', $resource->getController(), [ControllerInterface::class]))
            ->append($this->createClassNode('factory', $resource->getFactory(), [FactoryInterface::class]))
            ->append($this->createClassNode('repository', $resource->getRepository(), [RepositoryInterface::class]))
            ->append($this->createClassNode(
                'domain_manager',
                $resource->getDomainManager(),
                [DomainManagerInterface::class]
            ))
            ->append($this->createClassNode('form', $resource->getForm(), [FormTypeInterface::class]))
            ->append($this->createClassNode('choice_form', $resource->getChoiceForm(), [FormTypeInterface::class]))
            ->append($this->createNode('id_property_path', 'scalar', $resource->getIdPropertyPath()))
            ->append($this->createNode('label_property_path', 'scalar', $resource->getLabelPropertyPath()));

        if ($resource->getTranslation() !== null) {
            $childrenNode->append($this->createResourceNode($resource->getTranslation(), 'translation'));
        }

        return $resourceNode;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return ArrayNodeDefinition
     */
    private function createDriverNode(ResourceInterface $resource)
    {
        $driverNode = $this->createNode('driver')->addDefaultsIfNotSet();
        $driverNode->children()
            ->append($this->createNode('name', 'scalar', $resource->getDriver()))
            ->append($this->createNode('manager', 'scalar', $resource->getDriverManager()))
            ->append($this->createDriverMappingNode($resource));

        return $driverNode;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return ArrayNodeDefinition
     */
    private function createDriverMappingNode(ResourceInterface $resource)
    {
        $mappingNode = $this->createNode('mapping')->addDefaultsIfNotSet();
        $mappingNode->children()
            ->append($this->createNode('path', 'scalar', $resource->getDriverMappingPath()))
            ->append($this->createNode('format', 'scalar', $resource->getDriverMappingFormat()));

        return $mappingNode;
    }

    /**
     * @param string   $name
     * @param string   $class
     * @param string[] $interfaces
     *
     * @return NodeDefinition
     */
    private function createClassNode($name, $class, array $interfaces = [])
    {
        return $this->createNode($name, 'scalar', $class, function ($class) use ($interfaces) {
            if (!class_exists($class)) {
                return true;
            }

            $classInterfaces = class_implements($class);

            foreach ($interfaces as $interface) {
                if (!in_array($interface, $classInterfaces, true)) {
                    return true;
                }
            }

            return false;
        }, 'The %s %%s does not exist.');
    }

    /**
     * @param string        $name
     * @param string        $type
     * @param mixed         $default
     * @param callable|null $if
     * @param string|null   $then
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function createNode($name, $type = 'array', $default = null, $if = null, $then = null)
    {
        $node = $this->createTreeBuilder()->root($name, $type);

        if ($default !== null) {
            $node->defaultValue($default);
        }

        if ($if !== null && $then !== null) {
            $node->validate()->ifTrue($if)->thenInvalid(sprintf($then, $name));
        }

        return $node;
    }

    /**
     * @return TreeBuilder
     */
    private function createTreeBuilder()
    {
        return new TreeBuilder();
    }
}
