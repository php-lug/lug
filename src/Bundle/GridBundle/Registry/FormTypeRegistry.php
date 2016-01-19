<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Registry;

use Lug\Component\Registry\Model\ServiceRegistry;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FormTypeRegistry extends ServiceRegistry
{
    /**
     * @param FormTypeInterface[] $formTypes
     */
    public function __construct(array $formTypes = [])
    {
        parent::__construct(FormTypeInterface::class, $formTypes);
    }
}
