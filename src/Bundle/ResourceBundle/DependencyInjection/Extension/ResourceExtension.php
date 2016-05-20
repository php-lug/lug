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
use Lug\Component\Translation\Factory\TranslatableFactory;
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

            if ($resource->getTranslation() !== null) {
                $this->configureResource($resource->getTranslation(), $resourceConfig['translation']);
                $this->loadResource($resource->getTranslation(), $container);
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
        $resource->setDriver($config['driver']['name']);
        $resource->setDriverManager($config['driver']['manager']);
        $resource->setDriverMappingPath($config['driver']['mapping']['path']);
        $resource->setDriverMappingFormat($config['driver']['mapping']['format']);
        $resource->setModel($config['model']);
        $resource->setController($config['controller']);
        $resource->setFactory($config['factory']);
        $resource->setRepository($config['repository']);
        $resource->setDomainManager($config['domain_manager']);
        $resource->setForm($config['form']);
        $resource->setChoiceForm($config['choice_form']);
        $resource->setIdPropertyPath($config['id_property_path']);
        $resource->setLabelPropertyPath($config['label_property_path']);
    }

    /**
     * @param ResourceInterface $resource
     * @param ContainerBuilder  $container
     */
    private function loadResource(ResourceInterface $resource, ContainerBuilder $container)
    {
        $name = $resource->getName();
        $controller = 'lug.controller.'.$name;
        $factory = 'lug.factory.'.$name;
        $repository = 'lug.repository.'.$name;
        $domainManager = 'lug.domain_manager.'.$name;
        $form = 'lug.form.type.'.$name;
        $choiceForm = $form.'.choice';

        $container->setDefinition('lug.resource.'.$resource->getName(), $this->createResourceDefinition($resource));
        $container->setAlias('lug.manager.'.$resource->getName(), $this->createManagerAlias($resource));

        if (class_exists($resource->getController())) {
            $container->setDefinition($controller, $this->createControllerDefinition($resource));
        } elseif ($controller !== $resource->getController()) {
            $container->setAlias($container, $resource->getController());
        }

        if (class_exists($resource->getFactory())) {
            $container->setDefinition($factory, $this->createFactoryDefinition($resource));
        } elseif ($factory !== $resource->getFactory()) {
            $container->setAlias($factory, $resource->getFactory());
        }

        if (class_exists($resource->getRepository())) {
            $container->setDefinition($repository, $this->createRepositoryDefinition($resource));
        } elseif ($repository !== $resource->getRepository()) {
            $container->setAlias($repository, $resource->getRepository());
        }

        if (class_exists($resource->getDomainManager())) {
            $container->setDefinition($domainManager, $this->createDomainManagerDefinition($resource));
        } elseif ($domainManager !== $resource->getDomainManager()) {
            $container->setAlias($domainManager, $resource->getDomainManager());
        }

        if (class_exists($resource->getForm())) {
            $container->setDefinition($form, $this->createFormDefinition($resource));
        } elseif ($form !== $resource->getForm()) {
            $container->setAlias($form, $resource->getForm());
        }

        if (class_exists($resource->getChoiceForm())) {
            $container->setDefinition($choiceForm, $this->createChoiceFormDefinition($resource));
        } elseif ($choiceForm !== $resource->getChoiceForm()) {
            $container->setAlias($choiceForm, $resource->getChoiceForm());
        }

        $container->addClassResource(new \ReflectionClass($resource));
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createResourceDefinition(ResourceInterface $resource)
    {
        $arguments = [
            $resource->getName(),
            $resource->getDriver(),
            $resource->getDriverManager(),
            $resource->getDriverMappingPath(),
            $resource->getDriverMappingFormat(),
            $resource->getInterfaces(),
            $resource->getModel(),
            $resource->getController(),
            $resource->getFactory(),
            $resource->getRepository(),
            $resource->getDomainManager(),
            $resource->getForm(),
            $resource->getChoiceForm(),
            $resource->getIdPropertyPath(),
            $resource->getLabelPropertyPath(),
        ];

        if ($resource->getTranslation() !== null) {
            $arguments[] = new Reference('lug.resource.'.$resource->getTranslation()->getName());
        }

        $definition = new Definition(Resource::class, $arguments);
        $definition->addTag('lug.resource');

        return $definition;
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return string
     */
    private function createManagerAlias(ResourceInterface $resource)
    {
        if ($resource->getDriver() === ResourceInterface::DRIVER_DOCTRINE_MONGODB) {
            return 'doctrine_mongodb.odm.'.$resource->getDriverManager().'_document_manager';
        }

        return 'doctrine.orm.'.$resource->getDriverManager().'_entity_manager';
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return Definition
     */
    private function createFactoryDefinition(ResourceInterface $resource)
    {
        $arguments = [new Reference('lug.resource.'.$resource->getName())];

        if (is_a($resource->getFactory(), TranslatableFactory::class, true)) {
            $arguments[] = new Reference('lug.translation.context.locale');
        }

        $definition = new Definition($resource->getFactory(), $arguments);
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
    private function createControllerDefinition(ResourceInterface $resource)
    {
        $definition = new Definition($resource->getController(), [
            new Reference('lug.resource.'.$resource->getName()),
        ]);

        $definition->addMethodCall('setContainer', [new Reference('service_container')]);
        $definition->addTag('lug.controller', ['resource' => $resource->getName()]);

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
}
