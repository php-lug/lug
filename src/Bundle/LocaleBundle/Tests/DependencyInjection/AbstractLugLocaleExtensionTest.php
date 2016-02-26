<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests\DependencyInjection;

use Doctrine\ORM\EntityManagerInterface;
use Lug\Bundle\LocaleBundle\Context\LocaleContext;
use Lug\Bundle\LocaleBundle\DependencyInjection\LugLocaleExtension;
use Lug\Bundle\LocaleBundle\EventSubscriber\MenuSubscriber;
use Lug\Bundle\LocaleBundle\LugLocaleBundle;
use Lug\Bundle\LocaleBundle\Validator\LocaleIntegrityValidator;
use Lug\Bundle\ResourceBundle\DependencyInjection\Extension\ResourceExtension;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Locale\EventSubscriber\LocaleDomainSubscriber;
use Lug\Component\Locale\Form\Type\Doctrine\ORM\LocaleChoiceType;
use Lug\Component\Locale\Form\Type\LocaleCodeType;
use Lug\Component\Locale\Form\Type\LocaleType;
use Lug\Component\Locale\Model\Locale;
use Lug\Component\Locale\Negotiator\LocaleNegotiator;
use Lug\Component\Locale\Provider\LocaleProvider;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractLugLocaleExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LugLocaleExtension
     */
    private $extension;

    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStack;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private $localeRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->extension = new LugLocaleExtension(new LugLocaleBundle());
        $this->locale = 'en';
        $this->eventDispatcher = $this->createEventDispatcherMock();
        $this->requestStack = $this->createRequestStackMock();
        $this->translator = $this->createTranslatorMock();
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->container = new ContainerBuilder();
        $this->container->setParameter('locale', $this->locale);
        $this->container->set('phpunit', $this);
        $this->container->set('event_dispatcher', $this->eventDispatcher);
        $this->container->set('request_stack', $this->requestStack);
        $this->container->set('translator', $this->translator);
        $this->container->set('property_accessor', $this->propertyAccessor);
        $this->container->set('lug.resource.routing.parameter_resolver', $this->parameterResolver);

        $this->container->setDefinition(
            'doctrine.orm.default_entity_manager',
            $definition = new Definition(EntityManagerInterface::class)
        );

        $definition->setFactory([new Reference('phpunit'), 'getMock']);
        $definition->setArguments([EntityManagerInterface::class]);

        $this->container->registerExtension($this->extension);
        $this->container->loadFromExtension($this->extension->getAlias());
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ResourceExtension::class, $this->extension);
    }

    public function testContext()
    {
        $this->compileContainer();

        $this->assertTrue($this->container->hasDefinition($localeContextName = 'lug.locale.context'));
        $this->assertInstanceOf(LocaleContext::class, $this->container->get($localeContextName));
    }

    public function testEventSubscriber()
    {
        $this->compileContainer();

        $this->assertTrue($this->container->hasDefinition($domainSubscriberName = 'lug.locale.subscriber.domain'));
        $this->assertTrue($this->container->getDefinition($domainSubscriberName)->hasTag('kernel.event_subscriber'));
        $this->assertInstanceOf(LocaleDomainSubscriber::class, $this->container->get($domainSubscriberName));

        $this->assertTrue($this->container->hasDefinition($menuSubscriberName = 'lug.locale.subscriber.menu'));
        $this->assertTrue($this->container->getDefinition($menuSubscriberName)->hasTag('kernel.event_subscriber'));
        $this->assertInstanceOf(MenuSubscriber::class, $this->container->get($menuSubscriberName));
    }

    public function testForm()
    {
        $this->compileContainer();

        $localeFormName = 'lug.form.type.locale';
        $localeCodeFormName = 'lug.locale.form.type.locale_code';
        $localeChoiceFormName = 'lug.form.type.locale.choice';

        $this->assertTrue($this->container->hasDefinition($localeCodeFormName));
        $this->assertTrue($this->container->getDefinition($localeCodeFormName)->hasTag('form.type'));
        $this->assertInstanceOf(LocaleCodeType::class, $this->container->get($localeCodeFormName));

        $this->assertTrue($this->container->hasDefinition($localeFormName));
        $this->assertTrue($this->container->getDefinition($localeFormName)->hasTag('form.type'));
        $this->assertInstanceOf(LocaleType::class, $this->container->get($localeFormName));

        $this->assertTrue($this->container->hasDefinition($localeChoiceFormName));
        $this->assertTrue($this->container->getDefinition($localeChoiceFormName)->hasTag('form.type'));
        $this->assertInstanceOf(LocaleChoiceType::class, $this->container->get($localeChoiceFormName));
    }

    public function testNegotiator()
    {
        $this->compileContainer();

        $this->assertTrue($this->container->hasDefinition($localeNegotiatorName = 'lug.locale.negotiator'));
        $this->assertInstanceOf(LocaleNegotiator::class, $this->container->get($localeNegotiatorName));
    }

    public function testProvider()
    {
        $this->compileContainer();

        $this->assertTrue($this->container->hasDefinition($providerName = 'lug.locale.provider'));
        $this->assertInstanceOf(LocaleProvider::class, $this->container->get($providerName));
        $this->assertSame($this->locale, $this->container->getDefinition($providerName)->getArgument(1));
    }

    public function testValidator()
    {
        $this->compileContainer();

        $this->assertTrue($this->container->hasDefinition($validatorName = 'lug.locale.validator.integrity'));

        $validatorDefinition = $this->container->getDefinition($validatorName);

        $this->assertTrue($validatorDefinition->hasTag($validatorTag = 'validator.constraint_validator'));
        $this->assertSame([['alias' => 'lug_locale_integrity']], $validatorDefinition->getTag($validatorTag));

        $this->assertInstanceOf(LocaleIntegrityValidator::class, $this->container->get($validatorName));
    }

    public function testDefaultLocale()
    {
        $this->compileContainer('default_locale');

        $this->assertTrue($this->container->hasDefinition($providerName = 'lug.locale.provider'));
        $this->assertInstanceOf(LocaleProvider::class, $this->container->get($providerName));
        $this->assertSame('fr', $this->container->getDefinition($providerName)->getArgument(1));
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

        $this->container->get('doctrine.orm.default_entity_manager')
            ->expects($this->any())
            ->method('getRepository')
            ->with($this->identicalTo(Locale::class))
            ->will($this->returnValue($this->localeRepository = $this->createRepositoryMock()));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private function createEventDispatcherMock()
    {
        return $this->getMock(EventDispatcherInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->getMock(RequestStack::class);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createRepositoryMock()
    {
        return $this->getMock(RepositoryInterface::class);
    }
}
