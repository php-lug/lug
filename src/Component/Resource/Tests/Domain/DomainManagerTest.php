<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Tests\Domain;

use Doctrine\Common\Persistence\ObjectManager;
use Lug\Component\Resource\Domain\DomainEvent;
use Lug\Component\Resource\Domain\DomainManager;
use Lug\Component\Resource\Domain\DomainManagerInterface;
use Lug\Component\Resource\Exception\DomainException;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DomainManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainManager
     */
    private $domainManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private $repository;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = $this->createResourceMock();
        $this->eventDispatcher = $this->createEventDispatcherMock();
        $this->objectManager = $this->createObjectManagerMock();
        $this->repository = $this->createRepositoryMock();

        $this->domainManager = new DomainManager(
            $this->resource,
            $this->eventDispatcher,
            $this->objectManager,
            $this->repository
        );
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(DomainManagerInterface::class, $this->domainManager);
    }

    public function testFind()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->repository
            ->expects($this->once())
            ->method($repositoryMethod = 'findForShow')
            ->with(
                $this->identicalTo($criteria = ['criteria']),
                $this->identicalTo($sorting = ['sorting'])
            )
            ->will($this->returnValue($data = new \stdClass()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_find.'.($action = 'action')),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === null
                        && $event->getAction() === 'find.'.$action;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_find.'.$action),
                $this->callback(function (DomainEvent $event) use ($data, $action) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === 'find.'.$action;
                })
            );

        $this->assertSame($data, $this->domainManager->find($action, $repositoryMethod, $criteria, $sorting));
    }

    public function testFindThrowException()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->repository
            ->expects($this->once())
            ->method($repositoryMethod = 'findForShow')
            ->with(
                $this->identicalTo($criteria = ['criteria']),
                $this->identicalTo($sorting = ['sorting'])
            )
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_find.'.($action = 'action')),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === null
                        && $event->getAction() === 'find.'.$action;
                })
            );

        $statusCode = Response::HTTP_BAD_REQUEST;
        $message = 'message';

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_find.'.$action),
                $this->callback(function (DomainEvent $event) use ($action, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === null
                        && $event->getAction() === 'find.'.$action;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->find($action, $repositoryMethod, $criteria, $sorting);
            $this->fail();
        } catch (DomainException $e) {
            $this->assertSame($statusCode, $e->getStatusCode());
            $this->assertSame($message, $e->getMessage());
        }
    }

    public function testFindByPassException()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->repository
            ->expects($this->once())
            ->method($repositoryMethod = 'findForShow')
            ->with(
                $this->identicalTo($criteria = ['criteria']),
                $this->identicalTo($sorting = ['sorting'])
            )
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_find.'.($action = 'action')),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === null
                    && $event->getAction() === 'find.'.$action;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_find.'.$action),
                $this->callback(function (DomainEvent $event) use ($action) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === null
                        && $event->getAction() === 'find.'.$action;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->assertNull($this->domainManager->find($action, $repositoryMethod, $criteria, $sorting));
    }

    public function testCreate()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $data) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$createAction),
                $this->callback(function (DomainEvent $event) use ($data, $createAction) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $createAction;
                })
            );

        $this->domainManager->create($data);
    }

    public function testCreateWithoutFlush()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->never())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$createAction),
                $this->callback(function (DomainEvent $event) use ($data, $createAction) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $createAction;
                })
            );

        $this->domainManager->create($data, false);
    }

    public function testCreateThrowException()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $data) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $flushAction;
                })
            );

        $statusCode = Response::HTTP_BAD_REQUEST;
        $message = 'message';

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$createAction),
                $this->callback(function (DomainEvent $event) use ($data, $createAction, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $createAction;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->create($data);
            $this->fail();
        } catch (DomainException $e) {
            $this->assertSame($statusCode, $e->getStatusCode());
            $this->assertSame($message, $e->getMessage());
        }
    }

    public function testCreateByPassException()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$createAction),
                $this->callback(function (DomainEvent $event) use ($data, $createAction) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $createAction;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->domainManager->create($data);
    }

    public function testUpdateWithManagedObject()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($data = new \stdClass()))
            ->will($this->returnValue(true));

        $this->objectManager
            ->expects($this->never())
            ->method('persist');

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($data, $updateAction) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->domainManager->update($data);
    }

    public function testUpdateWithoutManagedObject()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($data = new \stdClass()))
            ->will($this->returnValue(false));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($data, $updateAction) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->domainManager->update($data);
    }

    public function testUpdateWithoutFlush()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->never())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($data, $updateAction) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->domainManager->update($data, false);
    }

    public function testUpdateThrowException()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($data = new \stdClass()))
            ->will($this->returnValue(false));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $statusCode = Response::HTTP_BAD_REQUEST;
        $message = 'message';

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($data, $updateAction, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $updateAction;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->update($data);
            $this->fail();
        } catch (DomainException $e) {
            $this->assertSame($statusCode, $e->getStatusCode());
            $this->assertSame($message, $e->getMessage());
        }
    }

    public function testUpdateByPassException()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('contains')
            ->with($this->identicalTo($data = new \stdClass()))
            ->will($this->returnValue(false));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($data));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($data, $updateAction) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $updateAction;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->domainManager->update($data);
    }

    public function testDelete()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$deleteAction),
                $this->callback(function (DomainEvent $event) use ($data, $deleteAction) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->domainManager->delete($data);
    }

    public function testDeleteWithoutFlush()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->never())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$deleteAction),
                $this->callback(function (DomainEvent $event) use ($data, $deleteAction) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->domainManager->delete($data, false);
    }

    public function testDeleteThrowException()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $statusCode = Response::HTTP_BAD_REQUEST;
        $message = 'message';

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$deleteAction),
                $this->callback(function (DomainEvent $event) use ($data, $deleteAction, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $deleteAction;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->delete($data);
            $this->fail();
        } catch (DomainException $e) {
            $this->assertSame($statusCode, $e->getStatusCode());
            $this->assertSame($message, $e->getMessage());
        }
    }

    public function testDeleteByPassException()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($data = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$deleteAction),
                $this->callback(function (DomainEvent $event) use ($data, $deleteAction) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === $data
                        && $event->getAction() === $deleteAction;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->domainManager->delete($data);
    }

    public function testFlush()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $data = new \stdClass();

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($action = 'flush')),
                $this->callback(function (DomainEvent $event) use ($action, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $action;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$action),
                $this->callback(function (DomainEvent $event) use ($action, $data) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === $data
                    && $event->getAction() === $action;
                })
            );

        $this->domainManager->flush($data);
    }

    public function testFlushWithoutObject()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($action = 'flush')),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === null
                    && $event->getAction() === $action;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$action),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === null
                    && $event->getAction() === $action;
                })
            );

        $this->domainManager->flush();
    }

    public function testFlushThrowException()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($action = 'flush')),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                    && $event->getData() === null
                    && $event->getAction() === $action;
                })
            );

        $statusCode = Response::HTTP_BAD_REQUEST;
        $message = 'message';

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$action),
                $this->callback(function (DomainEvent $event) use ($action, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === null
                        && $event->getAction() === $action;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->flush();
            $this->fail();
        } catch (DomainException $e) {
            $this->assertSame($statusCode, $e->getStatusCode());
            $this->assertSame($message, $e->getMessage());
        }
    }

    public function testFlushByPassException()
    {
        $this->resource
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($action = 'flush')),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                        && $event->getData() === null
                        && $event->getAction() === $action;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$action),
                $this->callback(function (DomainEvent $event) use ($action) {
                    $result = $event->getResource() === $this->resource
                        && $event->getData() === null
                        && $event->getAction() === $action;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->domainManager->flush();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->createMock(ResourceInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDispatcherInterface
     */
    private function createEventDispatcherMock()
    {
        return $this->createMock(EventDispatcherInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjectManager
     */
    private function createObjectManagerMock()
    {
        return $this->createMock(ObjectManager::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RepositoryInterface
     */
    private function createRepositoryMock()
    {
        return $this->createMock(RepositoryInterface::class);
    }
}
