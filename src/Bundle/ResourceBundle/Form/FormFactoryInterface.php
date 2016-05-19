<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Form;

use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Test\FormInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface FormFactoryInterface
{
    /**
     * @param ResourceInterface             $resource
     * @param string|FormTypeInterface|null $type
     * @param mixed                         $data
     * @param mixed[]                       $options
     *
     * @return FormInterface
     */
    public function create(ResourceInterface $resource, $type = null, $data = null, array $options = []);
}
