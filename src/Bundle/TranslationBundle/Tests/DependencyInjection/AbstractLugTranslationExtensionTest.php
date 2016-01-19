<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Tests\DependencyInjection;

use Doctrine\ODM\MongoDB\DocumentManager;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Bundle\TranslationBundle\Context\LocaleContext;
use Lug\Bundle\TranslationBundle\DependencyInjection\LugTranslationExtension;
use Lug\Bundle\TranslationBundle\EventSubscriber\TranslatableResourceSubscriber;
use Lug\Bundle\TranslationBundle\Form\Type\TranslatableType;
use Lug\Bundle\TranslationBundle\Provider\A2lixLocaleProvider;
use Lug\Bundle\TranslationBundle\Repository\Doctrine\MongoDB\TranslatableRepositoryFactory as DoctrineMongoDBTranslatableRepositoryFactory;
use Lug\Bundle\TranslationBundle\Repository\Doctrine\ORM\TranslatableRepositoryFactory as DoctrineORMTranslatableRepositoryFactory;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLugTranslationExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugTranslationExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStack;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new LugTranslationExtension();
        $this->requestStack = $this->createRequestStackMock();
        $this->localeRepository = $this->createLocaleRepositoryMock();
        $this->localeContext = $this->createLocaleContextMock();
        $this->localeProvider = $this->createLocaleProviderMock();
        $this->resourceRegistry = $this->createServiceRegistryMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->container = new ContainerBuilder();
        $this->container->set('request_stack', $this->requestStack);
        $this->container->set('lug.repository.locale', $this->localeRepository);
        $this->container->set('lug.locale.context', $this->localeContext);
        $this->container->set('lug.locale.provider', $this->localeProvider);
        $this->container->set('lug.resource.registry', $this->resourceRegistry);
        $this->container->set('lug.resource.routing.parameter_resolver', $this->parameterResolver);

        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension($this->extension->getAlias());
    }

    public function testContext()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            LocaleContext::class,
            $this->container->get('lug.translation.context.locale')
        );
    }

    public function testEventSubscriber()
    {
        $this->compileContainer();

        $eventSubscriber = $this->container->getDefinition(
            $eventSubscriberName = 'lug.translation.subscriber.translatable'
        );

        $this->assertTrue($eventSubscriber->hasTag('doctrine.event_subscriber'));
        $this->assertInstanceOf(TranslatableResourceSubscriber::class, $this->container->get($eventSubscriberName));
    }

    public function testForm()
    {
        $this->compileContainer();

        $form = $this->container->getDefinition($formName = 'lug.translation.form.type.translatable');

        $this->assertTrue($form->hasTag('form.type'));
        $this->assertInstanceOf(TranslatableType::class, $this->container->get($formName));
    }

    public function testProvider()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            A2lixLocaleProvider::class,
            $this->container->get('lug.translation.provider.a2lix')
        );
    }

    public function testRepository()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            DoctrineORMTranslatableRepositoryFactory::class,
            $this->container->get('lug.translation.repository.doctrine.orm.factory')
        );

        if (class_exists(DocumentManager::class)) {
            $this->assertInstanceOf(
                DoctrineMongoDBTranslatableRepositoryFactory::class,
                $this->container->get('lug.translation.repository.doctrine.mongodb.factory')
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $configuration
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    /**
     * @param string|null $configuration
     */
    private function compileContainer($configuration = null)
    {
        if ($configuration !== null) {
            $this->loadConfiguration($this->container, $configuration);
        }

        $this->container->compile();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createLocaleRepositoryMock()
    {
        return $this->getMock(RepositoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->getMock(RequestStack::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->getMock(LocaleContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private function createLocaleProviderMock()
    {
        return $this->getMock(LocaleProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }
}
