<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\DependencyInjection;

use Doctrine\ODM\MongoDB\DocumentManager;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Lug\Bundle\GridBundle\Handler\GridHandlerInterface;
use Lug\Bundle\ResourceBundle\DependencyInjection\Configurator\ResolveTargetSubscriberConfigurator;
use Lug\Bundle\ResourceBundle\DependencyInjection\LugResourceExtension;
use Lug\Bundle\ResourceBundle\EventListener\FlashListener;
use Lug\Bundle\ResourceBundle\EventListener\MessageListener;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\MongoDB\ResourceSubscriber as DoctrineMongoDBResourceSubscriber;
use Lug\Bundle\ResourceBundle\EventSubscriber\Doctrine\ORM\ResourceSubscriber as DoctrineORMResourceSubscriber;
use Lug\Bundle\ResourceBundle\EventSubscriber\RoutingSubscriber;
use Lug\Bundle\ResourceBundle\Form\EventSubscriber\CollectionSubscriber;
use Lug\Bundle\ResourceBundle\Form\EventSubscriber\FlashCsrfProtectionSubscriber;
use Lug\Bundle\ResourceBundle\Form\EventSubscriber\XmlHttpRequestSubscriber;
use Lug\Bundle\ResourceBundle\Form\Extension\CollectionExtension;
use Lug\Bundle\ResourceBundle\Form\Extension\DateExtension;
use Lug\Bundle\ResourceBundle\Form\Extension\DateTimeExtension;
use Lug\Bundle\ResourceBundle\Form\Extension\LabelButtonExtension;
use Lug\Bundle\ResourceBundle\Form\Extension\LabelFormExtension;
use Lug\Bundle\ResourceBundle\Form\Extension\TimeExtension;
use Lug\Bundle\ResourceBundle\Form\Extension\XmlHttpRequestExtension;
use Lug\Bundle\ResourceBundle\Form\FormFactory;
use Lug\Bundle\ResourceBundle\Form\Type\CsrfProtectionType;
use Lug\Bundle\ResourceBundle\Rest\Action\EventSubscriber\ApiActionSubscriber;
use Lug\Bundle\ResourceBundle\Rest\Action\EventSubscriber\ViewActionSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\FormViewSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\GridViewSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\PagerfantaViewSubscriber;
use Lug\Bundle\ResourceBundle\Rest\View\EventSubscriber\ResourceViewSubscriber;
use Lug\Bundle\ResourceBundle\Routing\CachedParameterResolver;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolver;
use Lug\Bundle\ResourceBundle\Security\SecurityChecker;
use Lug\Component\Resource\Form\Type\Doctrine\MongoDB\ResourceChoiceType as DoctrineMongoDBResourceChoiceType;
use Lug\Component\Resource\Form\Type\Doctrine\ORM\ResourceChoiceType as DoctrineORMResourceChoiceType;
use Lug\Component\Resource\Form\Type\ResourceType;
use Lug\Component\Resource\Registry\DomainManagerRegistry;
use Lug\Component\Resource\Registry\FactoryRegistry;
use Lug\Component\Resource\Registry\ManagerRegistry;
use Lug\Component\Resource\Registry\RepositoryRegistry;
use Lug\Component\Resource\Registry\ResourceRegistry;
use Lug\Component\Resource\Repository\Doctrine\MongoDB\RepositoryFactory as DoctrineMongoDBRepositoryFactory;
use Lug\Component\Resource\Repository\Doctrine\ORM\RepositoryFactory as DoctrineORMRepositoryFactory;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLugResourceExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugResourceExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private $session;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|FormRendererInterface
     */
    private $formRenderer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RouterInterface
     */
    private $router;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStack;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|GridHandlerInterface
     */
    private $gridHandler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->authorizationChecker = $this->createAuthorizationCheckerMock();
        $this->translator = $this->createTranslatorMock();
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->session = $this->createSessionMock();
        $this->formFactory = $this->createFormFactoryMock();
        $this->formRenderer = $this->createFormRendererMock();
        $this->registry = $this->createRegistryMock();
        $this->router = $this->createRouterMock();
        $this->requestStack = $this->createRequestStackMock();
        $this->gridHandler = $this->createGridHandler();

        $this->extension = new LugResourceExtension();
        $this->container = new ContainerBuilder();

        $this->container->set('security.authorization_checker', $this->authorizationChecker);
        $this->container->set('translator', $this->translator);
        $this->container->set('property_accessor', $this->propertyAccessor);
        $this->container->set('session', $this->session);
        $this->container->set('form.factory', $this->formFactory);
        $this->container->set('twig.form.renderer', $this->formRenderer);
        $this->container->set('doctrine', $this->registry);
        $this->container->set('router', $this->router);
        $this->container->set('request_stack', $this->requestStack);
        $this->container->set('lug.grid.handler', $this->gridHandler);

        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension($this->extension->getAlias());
    }

    public function testConfigurator()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            ResolveTargetSubscriberConfigurator::class,
            $this->container->get('lug.resource.configurator.resolve_target_entity')
        );
    }

    public function testEventListener()
    {
        $this->compileContainer();

        $this->assertInstanceOf(FlashListener::class, $this->container->get('lug.resource.listener.flash'));
        $this->assertInstanceOf(MessageListener::class, $this->container->get('lug.resource.listener.message'));
    }

    public function testEventSubscriber()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            DoctrineORMResourceSubscriber::class,
            $this->container->get('lug.resource.subscriber.doctrine.orm')
        );

        if (class_exists(DocumentManager::class)) {
            $this->assertInstanceOf(
                DoctrineMongoDBResourceSubscriber::class,
                $this->container->get('lug.resource.subscriber.doctrine.mongodb')
            );
        }

        $this->assertInstanceOf(RoutingSubscriber::class, $this->container->get('lug.resource.subscriber.routing'));
    }

    public function testForm()
    {
        $this->compileContainer();

        $this->assertInstanceOf(FormFactory::class, $this->container->get('lug.resource.form.factory'));

        $resourceType = 'lug.resource.form.type.resource';
        $this->assertTrue($this->container->getDefinition($resourceType)->hasTag('form.type'));
        $this->assertInstanceOf(ResourceType::class, $this->container->get($resourceType));

        $doctrineOrmResourceChoiceType = 'lug.resource.form.type.doctrine.orm.resource.choice';
        $this->assertTrue($this->container->getDefinition($doctrineOrmResourceChoiceType)->hasTag('form.type'));

        $this->assertInstanceOf(
            DoctrineORMResourceChoiceType::class,
            $this->container->get($doctrineOrmResourceChoiceType)
        );

        if (class_exists(DocumentManager::class)) {
            $doctrineMongoDbResourceChoiceType = 'lug.resource.form.type.doctrine.mongodb.resource.choice';
            $this->assertTrue($this->container->getDefinition($doctrineMongoDbResourceChoiceType)->hasTag('form.type'));

            $this->assertInstanceOf(
                DoctrineMongoDBResourceChoiceType::class,
                $this->container->get($doctrineMongoDbResourceChoiceType)
            );
        }

        $flashCsrfProtectionSubscriber = 'lug.resource.form.subscriber.flash_csrf_protection';

        $this->assertInstanceOf(
            FlashCsrfProtectionSubscriber::class,
            $this->container->get($flashCsrfProtectionSubscriber)
        );

        $csrfProtectionType = 'lug.resource.form.type.csrf_protection';
        $this->assertTrue($this->container->getDefinition($csrfProtectionType)->hasTag('form.type'));
        $this->assertInstanceOf(CsrfProtectionType::class, $this->container->get($csrfProtectionType));

        $collectionSubscriber = 'lug.resource.form.subscriber.collection';
        $this->assertInstanceOf(CollectionSubscriber::class, $this->container->get($collectionSubscriber));

        $collectionExtension = 'lug.resource.form.extension.collection';
        $this->assertTrue($this->container->getDefinition($collectionExtension)->hasTag('form.type_extension'));
        $this->assertInstanceOf(CollectionExtension::class, $this->container->get($collectionExtension));

        $labelFormExtension = 'lug.resource.form.extension.label_form';
        $this->assertTrue($this->container->getDefinition($labelFormExtension)->hasTag('form.type_extension'));
        $this->assertInstanceOf(LabelFormExtension::class, $this->container->get($labelFormExtension));

        $buttonFormExtension = 'lug.resource.form.extension.label_button';
        $this->assertTrue($this->container->getDefinition($buttonFormExtension)->hasTag('form.type_extension'));
        $this->assertInstanceOf(LabelButtonExtension::class, $this->container->get($buttonFormExtension));

        $datetimeExtension = 'lug.resource.form.extension.datetime';
        $this->assertTrue($this->container->getDefinition($datetimeExtension)->hasTag('form.type_extension'));
        $this->assertInstanceOf(DateTimeExtension::class, $this->container->get($datetimeExtension));

        $dateExtension = 'lug.resource.form.extension.date';
        $this->assertTrue($this->container->getDefinition($dateExtension)->hasTag('form.type_extension'));
        $this->assertInstanceOf(DateExtension::class, $this->container->get($dateExtension));

        $timeExtension = 'lug.resource.form.extension.time';
        $this->assertTrue($this->container->getDefinition($timeExtension)->hasTag('form.type_extension'));
        $this->assertInstanceOf(TimeExtension::class, $this->container->get($timeExtension));

        $xmlHttpRequestSubscriber = 'lug.resource.form.subscriber.xml_http_request';
        $this->assertInstanceOf(XmlHttpRequestSubscriber::class, $this->container->get($xmlHttpRequestSubscriber));

        $xmlHttpRequestExtension = 'lug.resource.form.extension.xml_http_request';
        $this->assertTrue($this->container->getDefinition($xmlHttpRequestExtension)->hasTag('form.type_extension'));
        $this->assertInstanceOf(XmlHttpRequestExtension::class, $this->container->get($xmlHttpRequestExtension));
    }

    public function testHateoas()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            PagerfantaFactory::class,
            $this->container->get('lug.resource.hateoas.pagerfanta_representation')
        );
    }

    public function testRegistry()
    {
        $this->compileContainer();

        $resourceRegistry = 'lug.resource.registry';
        $this->assertTrue($this->container->getDefinition($resourceRegistry)->hasTag('lug.registry'));
        $this->assertInstanceOf(ResourceRegistry::class, $this->container->get($resourceRegistry));

        $factoryRegistry = 'lug.resource.registry.factory';
        $this->assertTrue($this->container->getDefinition($factoryRegistry)->hasTag('lug.registry'));
        $this->assertInstanceOf(FactoryRegistry::class, $this->container->get($factoryRegistry));

        $managerRegistry = 'lug.resource.registry.manager';
        $this->assertTrue($this->container->getDefinition($managerRegistry)->hasTag('lug.registry'));
        $this->assertInstanceOf(ManagerRegistry::class, $this->container->get($managerRegistry));

        $repositoryRegistry = 'lug.resource.registry.repository';
        $this->assertTrue($this->container->getDefinition($repositoryRegistry)->hasTag('lug.registry'));
        $this->assertInstanceOf(RepositoryRegistry::class, $this->container->get($repositoryRegistry));

        $domainManagerRegistry = 'lug.resource.registry.domain_manager';
        $this->assertTrue($this->container->getDefinition($domainManagerRegistry)->hasTag('lug.registry'));
        $this->assertInstanceOf(DomainManagerRegistry::class, $this->container->get($domainManagerRegistry));
    }

    public function testRepository()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            DoctrineORMRepositoryFactory::class,
            $this->container->get('lug.resource.repository.doctrine.orm.factory')
        );

        if (class_exists(DocumentManager::class)) {
            $this->assertInstanceOf(
                DoctrineMongoDBRepositoryFactory::class,
                $this->container->get('lug.resource.repository.doctrine.mongodb.factory')
            );
        }
    }

    public function testRest()
    {
        $this->compileContainer();

        $viewActionSubscriber = 'lug.resource.rest.action.subscriber.view';
        $this->assertTrue($this->container->getDefinition($viewActionSubscriber)->hasTag('kernel.event_subscriber'));
        $this->assertInstanceOf(ViewActionSubscriber::class, $this->container->get($viewActionSubscriber));

        $apiActionSubscriber = 'lug.resource.rest.action.subscriber.api';
        $this->assertTrue($this->container->getDefinition($apiActionSubscriber)->hasTag('kernel.event_subscriber'));
        $this->assertInstanceOf(ApiActionSubscriber::class, $this->container->get($apiActionSubscriber));

        $formViewSubscriber = 'lug.resource.rest.view.subscriber.form';
        $this->assertTrue($this->container->getDefinition($formViewSubscriber)->hasTag('kernel.event_subscriber'));
        $this->assertInstanceOf(FormViewSubscriber::class, $this->container->get($formViewSubscriber));

        $gridViewSubscriber = 'lug.resource.rest.view.subscriber.grid';
        $this->assertTrue($this->container->getDefinition($gridViewSubscriber)->hasTag('kernel.event_subscriber'));
        $this->assertInstanceOf(GridViewSubscriber::class, $this->container->get($gridViewSubscriber));

        $pagerfantaViewSubscriber = 'lug.resource.rest.view.subscriber.pagerfanta';

        $this->assertTrue(
            $this->container->getDefinition($pagerfantaViewSubscriber)->hasTag('kernel.event_subscriber')
        );

        $this->assertInstanceOf(PagerfantaViewSubscriber::class, $this->container->get($pagerfantaViewSubscriber));

        $resourceViewSubscriber = 'lug.resource.rest.view.subscriber.resource';
        $this->assertTrue($this->container->getDefinition($resourceViewSubscriber)->hasTag('kernel.event_subscriber'));
        $this->assertInstanceOf(ResourceViewSubscriber::class, $this->container->get($resourceViewSubscriber));
    }

    public function testRouting()
    {
        $this->compileContainer();

        $this->assertInstanceOf(
            ParameterResolver::class,
            $this->container->get('lug.resource.routing.parameter_resolver.internal')
        );

        $this->assertInstanceOf(
            CachedParameterResolver::class,
            $this->container->get('lug.resource.routing.parameter_resolver')
        );
    }

    public function testSecurity()
    {
        $this->compileContainer();

        $this->assertInstanceOf(SecurityChecker::class, $this->container->get('lug.resource.security.checker'));
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
     * @return \PHPUnit_Framework_MockObject_MockObject|AuthorizationCheckerInterface
     */
    private function createAuthorizationCheckerMock()
    {
        return $this->getMock(AuthorizationCheckerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private function createTranslatorMock()
    {
        return $this->getMock(TranslatorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->getMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Session
     */
    private function createSessionMock()
    {
        return $this->getMock(Session::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormFactoryInterface
     */
    private function createFormFactoryMock()
    {
        return $this->getMock(FormFactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormRendererInterface
     */
    private function createFormRendererMock()
    {
        return $this->getMock(FormRendererInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createRegistryMock()
    {
        return $this->getMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RouterInterface
     */
    private function createRouterMock()
    {
        return $this->getMock(RouterInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->getMock(RequestStack::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridHandlerInterface
     */
    private function createGridHandler()
    {
        return $this->getMock(GridHandlerInterface::class);
    }
}
