<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Repository\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Repository\RepositoryFactory as RepositoryFactoryInterface;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\Doctrine\MongoDB\Repository;
use Lug\Component\Resource\Repository\Doctrine\MongoDB\RepositoryFactory;

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
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $resourceRegistry;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (!class_exists(DocumentManager::class)) {
            $this->markTestSkipped();
        }

        $this->resourceRegistry = $this->createResourceRegistryMock();
        $this->repositoryFactory = new RepositoryFactory($this->resourceRegistry);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(RepositoryFactoryInterface::class, $this->repositoryFactory);
    }

    public function testRepository()
    {
        $documentManager = $this->createDocumentManagerMock();
        $documentManager
            ->expects($this->exactly(2))
            ->method('getClassMetadata')
            ->with($this->identicalTo($documentName = 'document'))
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $documentManager
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->will($this->returnValue($configuration = $this->createConfigurationMock()));

        $configuration
            ->expects($this->exactly(2))
            ->method('getDefaultRepositoryClassName')
            ->will($this->returnValue($repositoryClass = DocumentRepository::class));

        $classMetadata
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($documentName));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([])));

        $documentManager
            ->expects($this->once())
            ->method('getUnitOfWork')
            ->will($this->returnValue($this->createUnitOfWorkMock()));

        $this->assertInstanceOf(
            $repositoryClass,
            $repository = $this->repositoryFactory->getRepository($documentManager, $documentName)
        );

        $this->assertSame($repository, $this->repositoryFactory->getRepository($documentManager, $documentName));
    }

    public function testResourceRepository()
    {
        $documentManager = $this->createDocumentManagerMock();
        $documentManager
            ->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->identicalTo($documentName = 'document'))
            ->will($this->returnValue($classMetadata = $this->createClassMetadataMock()));

        $classMetadata
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($documentName));

        $this->resourceRegistry
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$resource = $this->createResourceMock()])));

        $resource
            ->expects($this->once())
            ->method('getModel')
            ->will($this->returnValue($documentName));

        $documentManager
            ->expects($this->once())
            ->method('getUnitOfWork')
            ->will($this->returnValue($this->createUnitOfWorkMock()));

        $classMetadata->customRepositoryClassName = $repositoryClass = Repository::class;

        $this->assertInstanceOf(
            $repositoryClass,
            $repository = $this->repositoryFactory->getRepository($documentManager, $documentName)
        );

        $this->assertSame($repository, $this->repositoryFactory->getRepository($documentManager, $documentName));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createResourceRegistryMock()
    {
        return $this->createMock(RegistryInterface::class);
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
     * @return \PHPUnit_Framework_MockObject_MockObject|Configuration
     */
    private function createConfigurationMock()
    {
        return $this->createMock(Configuration::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }
}
