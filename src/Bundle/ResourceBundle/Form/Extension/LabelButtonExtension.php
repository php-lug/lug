<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LabelButtonExtension extends AbstractLabelExtension
{
    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ButtonType::class;
    }
}
