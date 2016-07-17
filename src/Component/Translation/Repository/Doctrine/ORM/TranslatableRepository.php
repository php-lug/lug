<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Translation\Context\LocaleContextInterface;
use Lug\Component\Translation\Repository\TranslatableRepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableRepository extends EntityRepository implements TranslatableRepositoryInterface
{
    use TranslatableRepositoryTrait {
        __construct as private constructFromTranslatableRepositoryTrait;
    }

    /**
     * @param EntityManager          $em
     * @param ClassMetadata          $class
     * @param ResourceInterface      $resource
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        $em,
        ClassMetadata $class,
        ResourceInterface $resource,
        LocaleContextInterface $localeContext
    ) {
        $this->constructFromTranslatableRepositoryTrait($em, $class, $resource, $localeContext);
    }
}
