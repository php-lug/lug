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
use Lug\Bundle\ResourceBundle\Util\ClassUtils;
use Lug\Component\Resource\Model\Resource;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceExtension extends ConfigurableExtension
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
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        if (!class_exists($class = ClassUtils::getNamespace($this).'\\Configuration')) {
            $class = ResourceConfiguration::class;
        }

        return new $class($this->bundle);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->bundle->getAlias();
    }

    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        foreach ($this->bundle->getResources() as $resource) {
            $resourceConfig = $config['resources'][$resource->getName()];

            $this->configureResource($resource, $resourceConfig);
            $this->loadResource($resource, $container);

            foreach ($resource->getRelations() as $name => $relation) {
                $this->configureResource($relation, $resourceConfig[$name]);
                $this->loadResource($relation, $container);
            }
        }

        $this->loadBundle($config, $container);
    }

    /**
     * @param mixed[]          $config
     * @param ContainerBuilder $container
     */
    protected function loadBundle(array $config, ContainerBuilder $container)
    {
    }

    /**
     * @param ResourceInterface $resource
     * @param mixed[]           $config
     */
    private function configureResource(ResourceInterface $resource, array $config)
    {
        $driverConfig = $config['driver'];
        $mappingConfig = $driverConfig['mapping'];

        $resource->setModel($config['model']);
        $resource->setDriver(isset($driverConfig['name']) ? $driverConfig['name'] : null);
        $resource->setDriverManager(isset($driverConfig['manager']) ? $driverConfig['manager'] : null);
        $resource->setDriverMappingPath(isset($mappingConfig['path']) ? $mappingConfig['path'] : null);
        $resource->setDriverMappingFormat(isset($mappingConfig['format']) ? $mappingConfig['format'] : null);
        $resource->setRepository(isset($config['repository']) ? $config['repository'] : null);
        $resource->setFactory(isset($config['factory']) ? $config['factory'] : null);
        $resource->setForm(isset($config['form']) ? $config['form'] : null);
        $resource->setChoiceForm(isset($config['choice_form']) ? $config['choice_form'] : null);
        $resource->setDomainManager(isset($config['domain_manager']) ? $config['domain_manager'] : null);
        $resource->setController(isset($config['controller']) ? $config['controller'] : null);
        $resource->setIdPropertyPath(isset($config['id_property_path']) ? $config['id_property_path'] : null);
        $resource->setLabelPropertyPath(isset($config['label_property_path']) ? $config['label_property_path'] : null);
    }

    /**
     * @param ResourceInterface $resource
     * @param ContainerBuilder  $container
     */
    private function loadResource(ResourceInterface $resource, ContainerBuilder $container)
    {
        $container->setDefinition('lug.resource.'.$resource->getName(), $this->createResourceDefinition($resource));

        if ($resource->getDriverManager() !== null) {
            $container->setAlias('lug.manager.'.$resource->getName(), $this->createManagerAlias($resource));
        }

        if ($resource->getRepository() !== null) {
            $repository = 'lug.repository.'.$resource->getName();

            if (class_exists($resource->getRepository())) {
                $container->setDefinition($repository, $this->createRepositoryDefinition($resource));
            } elseif ($repository !== $resource->getRepository()) {
                $container->setAlias($repository, $resource->getRepository());
            }
        }

        if ($resource->getFactory() !== null) {
            $factory = 'lug.factory.'.$resource->getName();

            if (class_exists($resource->getFactory())) {
                $container->setDefinition($factory, $this->createFactoryDefinition($resource));
            } elseif ($factory !== $resource->getFactory()) {
                $container->setAlias($factory, $resource->getFactory());
            }
        }

        if ($resource->getForm() !== null) {
            $form = 'lug.form.type.'.$resource->getName();

            if (class_exists($resource->getForm())) {
                $container->setDefinition($form, $this->createFormDefinition($resource));
            } elseif ($form !== $resource->getForm()) {
                $container->setAlias($form, $resource->getForm());
            }
        }

        if ($resource->getChoiceForm() !== null) {
            $choiceForm = 'lug.form.type.'.$resource->getName().'.choice';

            if (class_exists($resource->getChoiceForm())) {
                $container->setDefinition($choiceForm, $this->createChoiceFormDefinition($resource));
            } elseif ($choiceForm !== $resource->getChoiceForm()) {
                $container->setAlias($choiceForm, $resource->getChoiceForm());
            }
        }

        if ($resource->getDomainManager() !== null) {
            $domainManager = 'lug.domain_manager.'.$resource->getName();

            if (class_exists($resource->getDomainManager())) {
                $container->setDefinition($domainManager, $this->createDomainManagerDefinition($resource));
            } elseif ($domainManager !== $resource->getDomainManager()) {
                $container->setAlias($domainManager, $resource->getDomainManager());
            }
        }

        if ($resource->getController() !== null) {
            $controller = 'lug.controller.'.$resource->getName();

            if (class_exists($resource->getController())) {
                $container->setDefinition($controller, $this->createControllerDefinition($resource));
            } elseif ($controller !== $resource->getController()) {
                $container->setAlias($container, $resource->getController());
            }
        }

        $container->addObjectResource($resource);
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createResourceDefinition(ResourceInterface $resource)
    {
        $definition = new Definition(Resource::class, [
            $resource->getName(),
            $resource->getInterfaces(),
            $resource->getModel(),
        ]);

        $definition
            ->addMethodCall('setDriver', [$resource->getDriver()])
            ->addMethodCall('setDriverManager', [$resource->getDriverManager()])
            ->addMethodCall('setDriverMappingPath', [$resource->getDriverMappingPath()])
            ->addMethodCall('setDriverMappingFormat', [$resource->getDriverMappingFormat()])
            ->addMethodCall('setRepository', [$resource->getRepository()])
            ->addMethodCall('setFactory', [$resource->getFactory()])
            ->addMethodCall('setForm', [$resource->getForm()])
            ->addMethodCall('setChoiceForm', [$resource->getChoiceForm()])
            ->addMethodCall('setDomainManager', [$resource->getDomainManager()])
            ->addMethodCall('setController', [$resource->getController()])
            ->addMethodCall('setIdPropertyPath', [$resource->getIdPropertyPath()])
            ->addMethodCall('setLabelPropertyPath', [$resource->getLabelPropertyPath()])
            ->addTag('lug.resource');

        foreach ($resource->getRelations() as $name => $relation) {
            $definition->addMethodCall('addRelation', [$name, new Reference('lug.resource.'.$relation->getName())]);
        }

        return $definition;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return string
     */
    private function createManagerAlias(ResourceInterface $resource)
    {
        return $resource->getDriver() === ResourceInterface::DRIVER_DOCTRINE_MONGODB
            ? 'doctrine_mongodb.odm.'.$resource->getDriverManager().'_document_manager'
            : 'doctrine.orm.'.$resource->getDriverManager().'_entity_manager';
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createFactoryDefinition(ResourceInterface $resource)
    {
        $definition = new Definition($resource->getFactory(), [
            new Reference('lug.resource.'.$resource->getName()),
            new Reference('property_accessor'),
        ]);

        $definition->addTag('lug.factory', ['resource' => $resource->getName()]);

        return $definition;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createRepositoryDefinition(ResourceInterface $resource)
    {
        $definition = new Definition($resource->getRepository(), [$resource->getModel()]);
        $definition->setFactory([new Reference('lug.manager.'.$resource->getName()), 'getRepository']);
        $definition->addTag('lug.repository', ['resource' => $resource->getName()]);

        return $definition;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createFormDefinition(ResourceInterface $resource)
    {
        $definition = new Definition($resource->getForm(), [
            new Reference('lug.resource.'.$resource->getName()),
            new Reference('lug.factory.'.$resource->getName()),
        ]);

        $definition->addTag('form.type');

        return $definition;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createChoiceFormDefinition(ResourceInterface $resource)
    {
        $definition = new Definition($resource->getChoiceForm(), [
            new Reference('lug.resource.'.$resource->getName()),
        ]);

        $definition->addTag('form.type');

        return $definition;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createDomainManagerDefinition(ResourceInterface $resource)
    {
        $definition = new Definition($resource->getDomainManager(), [
            new Reference('lug.resource.'.$resource->getName()),
            new Reference('lug.resource.domain.event_dispatcher'),
            new Reference('lug.manager.'.$resource->getName()),
            new Reference('lug.repository.'.$resource->getName()),
        ]);

        $definition->addTag('lug.domain_manager', ['resource' => $resource->getName()]);

        return $definition;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createControllerDefinition(ResourceInterface $resource)
    {
        $definition = new Definition($resource->getController(), [
            new Reference('lug.resource.'.$resource->getName()),
        ]);

        $definition->addMethodCall('setContainer', [new Reference('service_container')]);
        $definition->addTag('lug.controller', ['resource' => $resource->getName()]);

        return $definition;
    }
}
