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

use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Lug\Component\Resource\Form\Type\Doctrine\ResourceChoiceType as DoctrineResourceChoiceType;

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
        return DocumentType::class;
    }
}
