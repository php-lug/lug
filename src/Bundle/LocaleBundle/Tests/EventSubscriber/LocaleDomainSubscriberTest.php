<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests\EventSubscriber;

use Lug\Bundle\LocaleBundle\EventSubscriber\LocaleDomainSubscriber;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Lug\Component\Resource\Domain\DomainEvent;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleDomainSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocaleDomainSubscriber
     */
    private $localeDomainSubscriber;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->localeProvider = $this->createLocaleProviderMock();
        $this->translator = $this->createTranslatorMock();
        $this->propertyAccessor = $this->createPropertyAccessorMock();

        $this->localeDomainSubscriber = new LocaleDomainSubscriber(
            $this->localeProvider,
            $this->translator,
            $this->propertyAccessor
        );
    }

    public function testValidateDefaultLocale()
    {
        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($locale = $this->createLocaleMock()));

        $domainEvent = $this->createDomainEventMock();
        $domainEvent
            ->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($locale));

        $domainEvent
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($resourceName = 'resource'));

        $resource
            ->expects($this->once())
            ->method('getLabelPropertyPath')
            ->will($this->returnValue($labelPropertyPath = 'code'));

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with($this->identicalTo($locale), $this->identicalTo($labelPropertyPath))
            ->will($this->returnValue($localeCode = 'code'));

        $domainEvent
            ->expects($this->once())
            ->method('setStopped')
            ->with($this->identicalTo(true));

        $domainEvent
            ->expects($this->once())
            ->method('setStatusCode')
            ->with($this->identicalTo(409));

        $domainEvent
            ->expects($this->once())
            ->method('setMessageType')
            ->with($this->identicalTo('error'));

        $domainEvent
            ->expects($this->once())
            ->method('getAction')
            ->will($this->returnValue('action'));

        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with(
                $this->identicalTo('lug.resource.action.default'),
                $this->identicalTo(['%resource%' => $localeCode]),
                $this->identicalTo('flashes')
            )
            ->will($this->returnValue($translation = 'translation'));

        $domainEvent
            ->expects($this->once())
            ->method('setMessage')
            ->with($this->identicalTo($translation));

        $this->localeDomainSubscriber->validateDefaultLocale($domainEvent);
    }

    public function testValidateNotDefaultLocale()
    {
        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($this->createLocaleMock()));

        $domainEvent = $this->createDomainEventMock();
        $domainEvent
            ->expects($this->once())
            ->method('getObject')
            ->will($this->returnValue($this->createLocaleMock()));

        $domainEvent
            ->expects($this->never())
            ->method('setStopped');

        $domainEvent
            ->expects($this->never())
            ->method('setStatusCode');

        $domainEvent
            ->expects($this->never())
            ->method('setMessageType');

        $domainEvent
            ->expects($this->never())
            ->method('setMessage');

        $this->localeDomainSubscriber->validateDefaultLocale($domainEvent);
    }

    public function testSubscribedEvents()
    {
        $this->assertSame(
            ['lug.locale.pre_delete' => 'validateDefaultLocale'],
            $this->localeDomainSubscriber->getSubscribedEvents()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private function createLocaleProviderMock()
    {
        return $this->getMock(LocaleProviderInterface::class);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|DomainEvent
     */
    private function createDomainEventMock()
    {
        return $this->getMockBuilder(DomainEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->getMock(LocaleInterface::class);
    }
}
