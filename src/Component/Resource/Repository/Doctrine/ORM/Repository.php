<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class Repository extends EntityRepository implements RepositoryInterface
{
    use RepositoryTrait {
        __construct as private constructTrait;
    }

    /**
     * @param EntityManagerInterface $em
     * @param ClassMetadata          $class
     * @param ResourceInterface      $resource
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $class, ResourceInterface $resource)
    {
        $this->constructTrait($em, $class, $resource);
    }
}
