<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Tests\Repository\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\MongoDB\Repository;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\MongoDB\RepositoryFactory;
use Lug\Bundle\TranslationBundle\Repository\Doctrine\MongoDB\TranslatableRepository;
use Lug\Bundle\TranslationBundle\Repository\Doctrine\MongoDB\TranslatableRepositoryFactory;
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
        if (!class_exists(DocumentManager::class)) {
            $this->markTestSkipped();
        }

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

    public function testTranslatableRepository()
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

        $classMetadata->customRepositoryClassName = $repositoryClass = TranslatableRepository::class;

        $this->assertInstanceOf(
            $repositoryClass,
            $repository = $this->repositoryFactory->getRepository($documentManager, $documentName)
        );

        $this->assertSame($repository, $this->repositoryFactory->getRepository($documentManager, $documentName));
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
     * @return \PHPUnit_Framework_MockObject_MockObject|DocumentManager
     */
    private function createDocumentManagerMock()
    {
        return $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UnitOfWork
     */
    public function createUnitOfWorkMock()
    {
        return $this->getMockBuilder(UnitOfWork::class)
            ->disableOriginalConstructor()
            ->getMock();
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
