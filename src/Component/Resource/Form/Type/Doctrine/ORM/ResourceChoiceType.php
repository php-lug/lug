<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Form\Type\Doctrine\ORM;

use Lug\Component\Resource\Form\Type\Doctrine\ResourceChoiceType as DoctrineResourceChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceChoiceType extends DoctrineResourceChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return EntityType::class;
    }
}
