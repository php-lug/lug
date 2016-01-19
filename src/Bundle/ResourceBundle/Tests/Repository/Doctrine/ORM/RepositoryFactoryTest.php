<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Repository\Doctrine\ORM;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Repository\RepositoryFactory as RepositoryFactoryInterface;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\Repository;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\RepositoryFactory;
use Lug\Component\Registry\Model\ServiceRegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private $resourceRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resourceRegistry = $this->createResourceRegistryMock();
        $this->repositoryFactory = new RepositoryFactory($this->resourceRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RepositoryFactoryInterface::class, $this->repositoryFactory);
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
            ->will($this->returnValue($entityName));

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
            ->will($this->returnValue($entityName));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($entityName));

        $classMetadata->customRepositoryClassName = $repositoryClass = Repository::class;

        $this->assertInstanceOf(
            $repositoryClass,
            $repository = $this->repositoryFactory->getRepository($entityManager, $entityName)
        );

        $this->assertSame($repository, $this->repositoryFactory->getRepository($entityManager, $entityName));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ServiceRegistryInterface
     */
    private function createResourceRegistryMock()
    {
        return $this->getMock(ServiceRegistryInterface::class);
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
