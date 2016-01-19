<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Form\Type\Doctrine\MongoDB;

use Lug\Component\Resource\Form\Type\Doctrine\AbstractResourceChoiceType as AbstractDoctrineResourceChoiceType;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractResourceChoiceType extends AbstractDoctrineResourceChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ResourceChoiceType::class;
    }
}
