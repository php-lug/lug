<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests\Validator;

use Lug\Bundle\LocaleBundle\Validator\LocaleIntegrityValidator;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleIntegrityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ConstraintValidatorFactory
     */
    private $constraintValidatorFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private $container;

    /**
     * @var LocaleIntegrityValidator
     */
    private $localeIntegrityValidator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = $this->createContainerMock();
        $this->localeProvider = $this->createLocaleProviderMock();
        $this->localeIntegrityValidator = new LocaleIntegrityValidator($this->localeProvider);
        $this->constraintValidatorFactory = new ConstraintValidatorFactory(
            $this->container,
            ['lug_locale_integrity' => $localeIntegrityService = 'lug.locale.validator.integrity']
        );

        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($this->identicalTo($localeIntegrityService))
            ->will($this->returnValue($this->localeIntegrityValidator));

        $this->validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory($this->constraintValidatorFactory)
            ->addXmlMapping(__DIR__.'/Fixtures/locale_integrity.xml')
            ->getValidator();
    }

    public function testValidateWithLocaleEnabledAndRequired()
    {
        $locale = $this->createLocaleMock();
        $locale
            ->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $locale
            ->expects($this->once())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $this->assertEmpty($this->validator->validate($locale));
    }

    public function testValidateWithDefaultLocaleDisabled()
    {
        $locale = $this->createLocaleMock();
        $locale
            ->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $locale
            ->expects($this->once())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($locale));

        $this->localeProvider
            ->expects($this->once())
            ->method('getRequiredLocales')
            ->will($this->returnValue([]));

        $errors = $this->validator->validate($locale);

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey(0, $errors);
        $this->assertSame('enabled', $errors[0]->getPropertyPath());
        $this->assertSame('The locale is the default one. You can\'t disable it.', $errors[0]->getMessage());
    }

    public function testValidateWithDefaultLocaleNotRequired()
    {
        $locale = $this->createLocaleMock();
        $locale
            ->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $locale
            ->expects($this->exactly(2))
            ->method('isRequired')
            ->will($this->returnValue(false));

        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($locale));

        $this->localeProvider
            ->expects($this->once())
            ->method('getRequiredLocales')
            ->will($this->returnValue([]));

        $errors = $this->validator->validate($locale);

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey(0, $errors);
        $this->assertSame('required', $errors[0]->getPropertyPath());
        $this->assertSame('The locale is the default one. You can\'t make it optional.', $errors[0]->getMessage());
    }

    public function testValidateWithLastLocaleDisabled()
    {
        $locale = $this->createLocaleMock();
        $locale
            ->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $locale
            ->expects($this->once())
            ->method('isRequired')
            ->will($this->returnValue(true));

        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($this->createLocaleMock()));

        $this->localeProvider
            ->expects($this->once())
            ->method('getRequiredLocales')
            ->will($this->returnValue([$locale]));

        $errors = $this->validator->validate($locale);

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey(0, $errors);
        $this->assertSame('enabled', $errors[0]->getPropertyPath());
        $this->assertSame('The locale is the last enabled. You can\'t disable it.', $errors[0]->getMessage());
    }

    public function testValidateWithLastLocaleNotRequired()
    {
        $locale = $this->createLocaleMock();
        $locale
            ->expects($this->exactly(2))
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $locale
            ->expects($this->exactly(2))
            ->method('isRequired')
            ->will($this->returnValue(false));

        $this->localeProvider
            ->expects($this->once())
            ->method('getDefaultLocale')
            ->will($this->returnValue($this->createLocaleMock()));

        $this->localeProvider
            ->expects($this->once())
            ->method('getRequiredLocales')
            ->will($this->returnValue([$locale]));

        $errors = $this->validator->validate($locale);

        $this->assertCount(1, $errors);
        $this->assertArrayHasKey(0, $errors);
        $this->assertSame('required', $errors[0]->getPropertyPath());
        $this->assertSame('The locale is the last required. You can\'t make it optional.', $errors[0]->getMessage());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private function createContainerMock()
    {
        return $this->createMock(ContainerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleProviderInterface
     */
    private function createLocaleProviderMock()
    {
        return $this->createMock(LocaleProviderInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->createMock(LocaleInterface::class);
    }
}
