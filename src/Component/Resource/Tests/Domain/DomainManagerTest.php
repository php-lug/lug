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
            ->will($this->returnValue($object = new \stdClass()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_find.'.($action = 'action')),
                $this->callback(function (DomainEvent $event) use ($action) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === null
                        && $event->getAction() === 'find.'.$action;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_find.'.$action),
                $this->callback(function (DomainEvent $event) use ($object, $action) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === 'find.'.$action;
                })
            );

        $this->assertSame($object, $this->domainManager->find($action, $repositoryMethod, $criteria, $sorting));
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
                        && $event->getObject() === null
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
                        && $event->getObject() === null
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
                    && $event->getObject() === null
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
                        && $event->getObject() === null
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $object) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$createAction),
                $this->callback(function (DomainEvent $event) use ($object, $createAction) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $createAction;
                })
            );

        $this->domainManager->create($object);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->never())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$createAction),
                $this->callback(function (DomainEvent $event) use ($object, $createAction) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $createAction;
                })
            );

        $this->domainManager->create($object, false);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $object) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                        && $event->getObject() === $object
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
                $this->callback(function (DomainEvent $event) use ($object, $createAction, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $createAction;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->create($object);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($createAction = 'create')),
                $this->callback(function (DomainEvent $event) use ($createAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $createAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$createAction),
                $this->callback(function (DomainEvent $event) use ($object, $createAction) {
                    $result = $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $createAction;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->domainManager->create($object);
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
            ->with($this->identicalTo($object = new \stdClass()))
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
                $this->callback(function (DomainEvent $event) use ($updateAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($object, $updateAction) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->domainManager->update($object);
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
            ->with($this->identicalTo($object = new \stdClass()))
            ->will($this->returnValue(false));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($object));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($object, $updateAction) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->domainManager->update($object);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->never())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($object, $updateAction) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->domainManager->update($object, false);
    }

    public function testUpdateThrowException()
    {
        $this->resource
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue($name = 'name'));

        $this->objectManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
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
                $this->callback(function (DomainEvent $event) use ($object, $updateAction, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $updateAction;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->update($object);
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
            ->method('persist')
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($updateAction = 'update')),
                $this->callback(function (DomainEvent $event) use ($updateAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $updateAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$updateAction),
                $this->callback(function (DomainEvent $event) use ($object, $updateAction) {
                    $result = $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $updateAction;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->domainManager->update($object);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$deleteAction),
                $this->callback(function (DomainEvent $event) use ($object, $deleteAction) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->domainManager->delete($object);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->never())
            ->method('flush');

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$deleteAction),
                $this->callback(function (DomainEvent $event) use ($object, $deleteAction) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->domainManager->delete($object, false);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
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
                $this->callback(function (DomainEvent $event) use ($object, $deleteAction, $statusCode, $message) {
                    $result = $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $deleteAction;

                    $event->setStatusCode($statusCode);
                    $event->setMessage($message);

                    return $result;
                })
            );

        try {
            $this->domainManager->delete($object);
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
            ->with($this->identicalTo($object = new \stdClass()));

        $this->objectManager
            ->expects($this->once())
            ->method('flush')
            ->will($this->throwException(new \Exception()));

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($deleteAction = 'delete')),
                $this->callback(function (DomainEvent $event) use ($deleteAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $deleteAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($flushAction = 'flush')),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(2))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$flushAction),
                $this->callback(function (DomainEvent $event) use ($flushAction, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $flushAction;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(3))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.error_'.$deleteAction),
                $this->callback(function (DomainEvent $event) use ($object, $deleteAction) {
                    $result = $event->getResource() === $this->resource
                        && $event->getObject() === $object
                        && $event->getAction() === $deleteAction;

                    $event->setStopped(false);

                    return $result;
                })
            );

        $this->domainManager->delete($object);
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

        $object = new \stdClass();

        $this->eventDispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.pre_'.($action = 'flush')),
                $this->callback(function (DomainEvent $event) use ($action, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $action;
                })
            );

        $this->eventDispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                $this->identicalTo('lug.'.$name.'.post_'.$action),
                $this->callback(function (DomainEvent $event) use ($action, $object) {
                    return $event->getResource() === $this->resource
                    && $event->getObject() === $object
                    && $event->getAction() === $action;
                })
            );

        $this->domainManager->flush($object);
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
                    && $event->getObject() === null
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
                    && $event->getObject() === null
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
                    && $event->getObject() === null
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
                        && $event->getObject() === null
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
                        && $event->getObject() === null
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
                        && $event->getObject() === null
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
