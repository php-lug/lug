<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Repository\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Repository extends DocumentRepository implements RepositoryInterface
{
    use RepositoryTrait {
        __construct as private constructTrait;
    }

    /**
     * @param DocumentManager   $dm
     * @param UnitOfWork        $uow
     * @param ClassMetadata     $class
     * @param ResourceInterface $resource
     */
    public function __construct(
        DocumentManager $dm,
        UnitOfWork $uow,
        ClassMetadata $class,
        ResourceInterface $resource
    ) {
        $this->constructTrait($dm, $uow, $class, $resource);
    }
}
