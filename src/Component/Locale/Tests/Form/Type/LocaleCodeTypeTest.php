<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Tests\Form\Type;

use Lug\Component\Locale\Form\Type\LocaleCodeType;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleCodeTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var LocaleCodeType
     */
    private $localeCodeType;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->repository = $this->createRepositoryMock();
        $this->localeCodeType = new LocaleCodeType($this->repository);

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->localeCodeType)
            ->getFormFactory();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->localeCodeType);
    }

    public function testSubmit()
    {
        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue([$locale = $this->createLocaleMock()]));

        $locale
            ->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('fr'));

        $form = $this->factory
            ->create(LocaleCodeType::class, $code = 'en')
            ->submit($code);

        $this->assertSame($code, $form->getData());
        $this->assertCount(563, $form->createView()->vars['choices']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createRepositoryMock()
    {
        return $this->getMock(RepositoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleInterface
     */
    private function createLocaleMock()
    {
        return $this->getMock(LocaleInterface::class);
    }
}
