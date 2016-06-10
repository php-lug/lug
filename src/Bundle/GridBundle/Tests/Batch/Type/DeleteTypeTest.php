<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Batch\Type;

use Lug\Bundle\GridBundle\Batch\Type\DeleteType;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Grid\Batch\Type\AbstractType;
use Lug\Component\Grid\Model\GridInterface;
use Lug\Component\Registry\Model\RegistryInterface;
use Lug\Component\Resource\Domain\DomainManagerInterface;
use Lug\Component\Resource\Exception\DomainException;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DeleteTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DeleteType
     */
    private $type;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private $domainManagerRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->domainManagerRegistry = $this->createServiceRegistryMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->type = new DeleteType($this->domainManagerRegistry, $this->parameterResolver);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
    }

    public function testBatch()
    {
        $data = [$object = new \stdClass()];

        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->domainManagerRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($domainManager = $this->createDomainManagerMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $domainManager
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->identicalTo($object),
                $this->isTrue()
            );

        $this->type->batch($data, ['grid' => $grid]);
    }

    public function testBatchWithDomainException()
    {
        $data = [$object = new \stdClass()];

        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->domainManagerRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($domainManager = $this->createDomainManagerMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(false));

        $domainManager
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->identicalTo($object),
                $this->isTrue()
            )
            ->will($this->throwException($this->createDomainExceptionMock()));

        $this->type->batch($data, ['grid' => $grid]);
    }

    public function testBatchWithApi()
    {
        $data = [$object = new \stdClass()];

        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->domainManagerRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($domainManager = $this->createDomainManagerMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $domainManager
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->identicalTo($object),
                $this->isFalse()
            );

        $domainManager
            ->expects($this->once())
            ->method('flush');

        $this->type->batch($data, ['grid' => $grid]);
    }

    public function testBatchWithApiAndDomainException()
    {
        $data = [$object = new \stdClass()];

        $grid = $this->createGridMock();
        $grid
            ->expects($this->once())
            ->method('getResource')
            ->will($this->returnValue($resource = $this->createResourceMock()));

        $resource
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->domainManagerRegistry
            ->expects($this->once())
            ->method('offsetGet')
            ->with($this->identicalTo($name))
            ->will($this->returnValue($domainManager = $this->createDomainManagerMock()));

        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $domainManager
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->identicalTo($object),
                $this->isFalse()
            )
            ->will($this->throwException($exception = $this->createDomainExceptionMock()));

        try {
            $this->type->batch($data, ['grid' => $grid]);
            $this->fail();
        } catch (DomainException $e) {
            $this->assertSame($exception, $e);
        }
    }

    public function testBatchWithoutArray()
    {
        $this->domainManagerRegistry
            ->expects($this->never())
            ->method('offsetGet');

        $this->type->batch('data', []);
    }

    public function testName()
    {
        $this->assertSame('delete', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RegistryInterface
     */
    private function createServiceRegistryMock()
    {
        return $this->createMock(RegistryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->createMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DomainManagerInterface
     */
    private function createDomainManagerMock()
    {
        return $this->createMock(DomainManagerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridInterface
     */
    private function createGridMock()
    {
        return $this->createMock(GridInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|DomainException
     */
    private function createDomainExceptionMock()
    {
        return $this->getMockBuilder(DomainException::class)
            ->setMethods(['getMessage'])
            ->getMock();
    }
}
