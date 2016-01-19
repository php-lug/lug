<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Tests\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Lug\Bundle\TranslationBundle\EventSubscriber\TranslatableResourceSubscriber;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Model\TranslatableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableResourceSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslatableResourceSubscriber
     */
    private $translatableResourceSubscriber;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private $localeContext;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = $this->createContainerMock();
        $this->resourceRegistry = $this->createResourceRegistryMock();
        $this->localeContext = $this->createLocaleContextMock();

        $this->container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap([
                [
                    'lug.resource.registry',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->resourceRegistry,
                ],
                [
                    'lug.translation.context.locale',
                    ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE,
                    $this->localeContext,
                ],
            ]));

        $this->translatableResourceSubscriber = new TranslatableResourceSubscriber($this->container);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(EventSubscriber::class, $this->translatableResourceSubscriber);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame([Events::postLoad], $this->translatableResourceSubscriber->getSubscribedEvents());
    }

    public function testPostLoadWithTranslatable()
    {
        $event = $this->createLifecycleEventArgsMock();
        $event
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($translatable = $this->createTranslatableMock()));

        $this->localeContext
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue($locales = ['fr']));

        $translatable
            ->expects($this->once())
            ->method('setLocales')
            ->with($this->identicalTo($locales));

        $this->localeContext
            ->expects($this->once())
            ->method('getFallbackLocale')
            ->will($this->returnValue($fallbackLocale = 'en'));

        $translatable
            ->expects($this->once())
            ->method('setFallbackLocale')
            ->with($this->identicalTo($fallbackLocale));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue(get_class($translatable)));

        $resource
            ->expects($this->once())
            ->method('getTranslation')
            ->will($this->returnValue($translation = $this->createResourceMock()));

        $translation
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model = 'model'));

        $translatable
            ->expects($this->once())
            ->method('setTranslationClass')
            ->with($this->identicalTo($model));

        $this->translatableResourceSubscriber->postLoad($event);
    }

    public function testPostLoadWithoutTranslatable()
    {
        $event = $this->createLifecycleEventArgsMock();
        $event
            ->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($entity = $this->createStdClassMock()));

        $entity
            ->expects($this->never())
            ->method('setCurrentLocale');

        $entity
            ->expects($this->never())
            ->method('setFallbackLocale');

        $entity
            ->expects($this->never())
            ->method('setTranslationClass');

        $this->translatableResourceSubscriber->postLoad($event);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private function createContainerMock()
    {
        return $this->getMock(ContainerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createResourceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->getMock(LocaleContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LifecycleEventArgs
     */
    private function createLifecycleEventArgsMock()
    {
        return $this->getMockBuilder(LifecycleEventArgs::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|TranslatableInterface
     */
    private function createTranslatableMock()
    {
        return $this->getMock(TranslatableInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\stdClass
     */
    private function createStdClassMock()
    {
        return $this->getMock(\stdClass::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
