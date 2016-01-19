<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\TranslationBundle\Form\Type;

use Lug\Component\Resource\Form\Type\AbstractResourceType;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractTranslatableType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TranslatableType::class;
    }
}
