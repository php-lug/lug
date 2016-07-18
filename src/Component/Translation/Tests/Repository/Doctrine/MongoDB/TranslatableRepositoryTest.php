<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Tests\Repository\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Model\TranslatableInterface;
use Lug\Component\Translation\Model\TranslatableTrait;
use Lug\Component\Translation\Model\TranslationInterface;
use Lug\Component\Translation\Model\TranslationTrait;
use Lug\Component\Translation\Repository\Doctrine\MongoDB\TranslatableRepository;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslatableRepository
     */
    private $translatableRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DocumentManager
     */
    private $documentManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UnitOfWork
     */
    private $unitOfWork;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private $classMetadata;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var string
     */
    private $class;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private $localeContext;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists(DocumentManager::class)) {
            $this->markTestSkipped();
        }

        $this->documentManager = $this->createDocumentManagerMock();
        $this->unitOfWork = $this->createUnitOfWorkMock();
        $this->classMetadata = $this->createClassMetadataMock();
        $this->resource = $this->createResourceMock();
        $this->localeContext = $this->createLocaleContextMock();
        $this->class = $this->classMetadata->name = TranslatableTest::class;

        $this->translatableRepository = new TranslatableRepository(
            $this->documentManager,
            $this->unitOfWork,
            $this->classMetadata,
            $this->resource,
            $this->localeContext
        );
    }

    public function testCreateQueryBuilderForCollection()
    {
        $this->classMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue([]));

        $this->classMetadata
            ->expects($this->once())
            ->method('getEmbeddedFieldsMappings')
            ->will($this->returnValue([]));

        $this->classMetadata
            ->expects($this->once())
            ->method('getAssociationTargetClass')
            ->with($this->identicalTo('translations'))
            ->will($this->returnValue($translationClass = TranslationTest::class));

        $translationClassMetadata = $this->createClassMetadataMock();
        $translationClassMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue(['locale']));

        $translationClassMetadata
            ->expects($this->once())
            ->method('getEmbeddedFieldsMappings')
            ->will($this->returnValue([]));

        $this->documentManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($translationClass))
            ->will($this->returnValue($translationClassMetadata));

        $this->documentManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder = $this->createQueryBuilderMock()));

        $queryBuilder
            ->expects($this->once())
            ->method('field')
            ->with($this->identicalTo('translations.locale'))
            ->will($this->returnSelf());

        $this->localeContext
            ->expects($this->once())
            ->method('getLocales')
            ->will($this->returnValue($locales = ['fr']));

        $this->localeContext
            ->expects($this->once())
            ->method('getFallbackLocale')
            ->will($this->returnValue($fallbackLocale = 'en'));

        $queryBuilder
            ->expects($this->once())
            ->method('in')
            ->with($this->identicalTo(array_merge($locales, [$fallbackLocale])))
            ->will($this->returnSelf());

        $this->assertSame($queryBuilder, $this->translatableRepository->createQueryBuilderForCollection());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DocumentManager
     */
    private function createDocumentManagerMock()
    {
        return $this->createMock(DocumentManager::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UnitOfWork
     */
    private function createUnitOfWorkMock()
    {
        return $this->createMock(UnitOfWork::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function createClassMetadataMock()
    {
        return $this->createMock(ClassMetadata::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->createMock(LocaleContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Builder
     */
    private function createQueryBuilderMock()
    {
        return $this->createMock(Builder::class);
    }
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableTest implements TranslatableInterface
{
    use TranslatableTrait;
}

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslationTest implements TranslationInterface
{
    use TranslationTrait;
}
