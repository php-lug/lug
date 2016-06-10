<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Tests\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Model\TranslatableInterface;
use Lug\Component\Translation\Model\TranslatableTrait;
use Lug\Component\Translation\Model\TranslationInterface;
use Lug\Component\Translation\Model\TranslationTrait;
use Lug\Component\Translation\Repository\Doctrine\ORM\TranslatableRepository;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    private $entityManager;

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
        $this->entityManager = $this->createEntityManagerMock();
        $this->classMetadata = $this->createClassMetadataMock();
        $this->resource = $this->createResourceMock();
        $this->localeContext = $this->createLocaleContextMock();
        $this->class = $this->classMetadata->name = TranslatableTest::class;

        $this->translatableRepository = new TranslatableRepository(
            $this->entityManager,
            $this->classMetadata,
            $this->resource,
            $this->localeContext
        );
    }

    public function testCreateQueryBuilder()
    {
        $this->classMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue([]));

        $this->classMetadata
            ->expects($this->once())
            ->method('getAssociationMapping')
            ->with($this->identicalTo('translations'))
            ->will($this->returnValue(['targetEntity' => $translationClass = TranslationTest::class]));

        $translationClassMetadata = $this->createClassMetadataMock();
        $translationClassMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue([]));

        $this->entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($translationClass))
            ->will($this->returnValue($translationClassMetadata));

        $this->resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'resource'));

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder = $this->createQueryBuilderMock()));

        $queryBuilder
            ->expects($this->once())
            ->method('select')
            ->with($this->identicalTo($name))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('from')
            ->with(
                $this->identicalTo($this->class),
                $this->identicalTo($name),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('addSelect')
            ->with($this->identicalTo('resource_translation'))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->exactly(2))
            ->method('getRootAliases')
            ->will($this->returnValue([$name]));

        $queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->with(
                $this->identicalTo($name.'.translations'),
                $this->identicalTo('resource_translation'),
                $this->isNull(),
                $this->isNull(),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $this->assertSame($queryBuilder, $this->translatableRepository->createQueryBuilder());
    }

    public function testCreateQueryBuilderForCollection()
    {
        $this->classMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue([]));

        $this->classMetadata
            ->expects($this->once())
            ->method('getAssociationMapping')
            ->with($this->identicalTo('translations'))
            ->will($this->returnValue(['targetEntity' => $translationClass = TranslationTest::class]));

        $translationClassMetadata = $this->createClassMetadataMock();
        $translationClassMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue([]));

        $this->entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($translationClass))
            ->will($this->returnValue($translationClassMetadata));

        $this->resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'resource'));

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder = $this->createQueryBuilderMock()));

        $queryBuilder
            ->expects($this->once())
            ->method('select')
            ->with($this->identicalTo($name))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('from')
            ->with(
                $this->identicalTo($this->class),
                $this->identicalTo($name),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('addSelect')
            ->with($this->identicalTo('resource_translation'))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->exactly(3))
            ->method('getRootAliases')
            ->will($this->returnValue([$name]));

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
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->once())
            ->method('in')
            ->with(
                $this->identicalTo('resource.locale'),
                $this->identicalTo(array_merge($locales, [$fallbackLocale]))
            )
            ->will($this->returnValue($expression = 'expression'));

        $queryBuilder
            ->expects($this->once())
            ->method('innerJoin')
            ->with(
                $this->identicalTo($name.'.translations'),
                $this->identicalTo('resource_translation'),
                $this->identicalTo(Join::WITH),
                $this->identicalTo($expression),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $this->assertSame($queryBuilder, $this->translatableRepository->createQueryBuilderForCollection());
    }

    public function testFindOneBy()
    {
        $this->classMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue([
                $translatableField = 'translatable_field',
                $bothField = 'both_field',
            ]));

        $this->classMetadata
            ->expects($this->once())
            ->method('getAssociationMapping')
            ->with($this->identicalTo('translations'))
            ->will($this->returnValue(['targetEntity' => $translationClass = TranslationTest::class]));

        $translationClassMetadata = $this->createClassMetadataMock();
        $translationClassMetadata
            ->expects($this->once())
            ->method('getFieldNames')
            ->will($this->returnValue([
                $translationField = 'translation_field',
                $bothField,
            ]));

        $this->entityManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($translationClass))
            ->will($this->returnValue($translationClassMetadata));

        $this->resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'resource'));

        $this->entityManager
            ->expects($this->once())
            ->method('createQueryBuilder')
            ->will($this->returnValue($queryBuilder = $this->createQueryBuilderMock()));

        $queryBuilder
            ->expects($this->once())
            ->method('select')
            ->with($this->identicalTo($name))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('from')
            ->with(
                $this->identicalTo($this->class),
                $this->identicalTo($name),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('addSelect')
            ->with($this->identicalTo('resource_translation'))
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->exactly(5))
            ->method('getRootAliases')
            ->will($this->returnValue([$name]));

        $queryBuilder
            ->expects($this->once())
            ->method('leftJoin')
            ->with(
                $this->identicalTo($name.'.translations'),
                $this->identicalTo($translation = 'resource_translation'),
                $this->isNull(),
                $this->isNull(),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->exactly(3))
            ->method('expr')
            ->will($this->returnValue($expr = $this->createExprMock()));

        $expr
            ->expects($this->at(0))
            ->method('eq')
            ->with(
                $this->identicalTo($name.'.'.$translatableField),
                $this->matchesRegularExpression('/:'.$name.'_'.$translatableField.'_[a-z0-9]{22}/')
            )
            ->will($this->returnValue($translatableWhere = 'translatable_where'));

        $expr
            ->expects($this->at(1))
            ->method('eq')
            ->with(
                $this->identicalTo($translation.'.'.$translationField),
                $this->matchesRegularExpression('/:'.$translation.'_'.$translationField.'_[a-z0-9]{22}/')
            )
            ->will($this->returnValue($translationWhere = 'translation_where'));

        $expr
            ->expects($this->at(2))
            ->method('eq')
            ->with(
                $this->identicalTo($name.'.'.$bothField),
                $this->matchesRegularExpression('/:'.$name.'_'.$bothField.'_[a-z0-9]{22}/')
            )
            ->will($this->returnValue($bothWhere = 'both_where'));

        $queryBuilder
            ->expects($this->exactly(3))
            ->method('andWhere')
            ->will($this->returnValueMap([
                [$translatableWhere, $queryBuilder],
                [$translationWhere, $queryBuilder],
                [$bothWhere, $queryBuilder],
            ]));

        $queryBuilder
            ->expects($this->at(9))
            ->method('setParameter')
            ->with(
                $this->matchesRegularExpression('/'.$name.'_'.$translatableField.'_[a-z0-9]{22}/'),
                $this->identicalTo($translatableValue = 'translatable_value'),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->at(13))
            ->method('setParameter')
            ->with(
                $this->matchesRegularExpression('/'.$translation.'_'.$translationField.'_[a-z0-9]{22}/'),
                $this->identicalTo($translationValue = 'translation_value'),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->at(17))
            ->method('setParameter')
            ->with(
                $this->matchesRegularExpression('/'.$name.'_'.$bothField.'_[a-z0-9]{22}/'),
                $this->identicalTo($bothValue = 'both_value'),
                $this->isNull()
            )
            ->will($this->returnSelf());

        $queryBuilder
            ->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($query = $this->createQueryMock()));

        $query
            ->expects($this->once())
            ->method('getOneOrNullResult')
            ->will($this->returnValue($result = 'result'));

        $this->assertSame($result, $this->translatableRepository->findOneBy([
            $translatableField => $translatableValue,
            $translationField  => $translationValue,
            $bothField         => $bothValue,
        ]));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    private function createEntityManagerMock()
    {
        return $this->createMock(EntityManager::class);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|QueryBuilder
     */
    private function createQueryBuilderMock()
    {
        return $this->createMock(QueryBuilder::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Expr
     */
    private function createExprMock()
    {
        return $this->createMock(Expr::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Query
     */
    private function createQueryMock()
    {
        return $this->getMockBuilder(\stdClass::class)
            ->setMethods(['getOneOrNullResult'])
            ->getMock();
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
