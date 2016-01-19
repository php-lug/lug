<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Tests\Repository\Doctrine\ORM;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\Repository;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\RepositoryFactory;
use Lug\Bundle\TranslationBundle\Repository\Doctrine\ORM\TranslatableRepository;
use Lug\Bundle\TranslationBundle\Repository\Doctrine\ORM\TranslatableRepositoryFactory;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableRepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TranslatableRepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
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
        $this->resourceRegistry = $this->createServiceRegistryMock();
        $this->localeContext = $this->createLocaleContextMock();

        $this->container = $this->createContainerMock();
        $this->container
            ->expects($this->atLeast(1))
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

        $this->repositoryFactory = new TranslatableRepositoryFactory($this->container);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RepositoryFactory::class, $this->repositoryFactory);
    }

    public function testRepository()
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager
            ->expects($this->exactly(3))
            ->method('getClassMetadata')
            ->with($this->identicalTo($entityName = 'entity'))
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $entityManager
            ->expects($this->once())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration = $this->createConfigurationMock()));

        $configuration
            ->expects($this->once())
            ->method('getDefaultRepositoryClassName')
            ->will($this->returnValue($repositoryClass = EntityRepository::class));

        $classMetadata
            ->expects($this->exactly(3))
            ->method('getName')
            ->will($this->returnValue($model = 'name'));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([])));

        $this->assertInstanceOf(
            $repositoryClass,
            $repository = $this->repositoryFactory->getRepository($entityManager, $entityName)
        );

        $this->assertSame($repository, $this->repositoryFactory->getRepository($entityManager, $entityName));
    }

    public function testResourceRepository()
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager
            ->expects($this->exactly(3))
            ->method('getClassMetadata')
            ->with($this->identicalTo($entityName = 'entity'))
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $classMetadata
            ->expects($this->exactly(3))
            ->method('getName')
            ->will($this->returnValue($model = 'name'));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model));

        $classMetadata->customRepositoryClassName = $repositoryClass = Repository::class;

        $this->assertInstanceOf(
            $repositoryClass,
            $repository = $this->repositoryFactory->getRepository($entityManager, $entityName)
        );

        $this->assertSame($repository, $this->repositoryFactory->getRepository($entityManager, $entityName));
    }

    public function testTranslatableRepository()
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager
            ->expects($this->exactly(3))
            ->method('getClassMetadata')
            ->with($this->identicalTo($entityName = 'entity'))
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $classMetadata
            ->expects($this->exactly(3))
            ->method('getName')
            ->will($this->returnValue($model = 'name'));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($model));

        $classMetadata->customRepositoryClassName = $repositoryClass = TranslatableRepository::class;

        $this->assertInstanceOf(
            $repositoryClass,
            $repository = $this->repositoryFactory->getRepository($entityManager, $entityName)
        );

        $this->assertSame($repository, $this->repositoryFactory->getRepository($entityManager, $entityName));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
     */
    private function createContainerMock()
    {
        return $this->getMock(ContainerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|LocaleContextInterface
     */
    private function createLocaleContextMock()
    {
        return $this->getMock(LocaleContextInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityManagerInterface
     */
    private function createEntityManagerMock()
    {
        return $this->getMock(EntityManagerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ClassMetadata
     */
    private function createClassMetadataMock()
    {
        return $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Configuration
     */
    private function createConfigurationMock()
    {
        return $this->getMock(Configuration::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
