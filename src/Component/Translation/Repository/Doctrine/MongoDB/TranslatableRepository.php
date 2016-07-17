<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Repository\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Repository\TranslatableRepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableRepository extends DocumentRepository implements TranslatableRepositoryInterface
{
    use TranslatableRepositoryTrait {
        __construct as private constructFromTranslatableRepositoryTrait;
    }

    /**
     * @param DocumentManager        $dm
     * @param UnitOfWork             $uow
     * @param ClassMetadata          $class
     * @param ResourceInterface      $resource
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        DocumentManager $dm,
        UnitOfWork $uow,
        ClassMetadata $class,
        ResourceInterface $resource,
        LocaleContextInterface $localeContext
    ) {
        $this->constructFromTranslatableRepositoryTrait($dm, $uow, $class, $resource, $localeContext);
    }
}
