<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Form\Validator;

use Lug\Component\Grid\Model\GridInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridSortingConstraint extends Constraint
{
    /**
     * @var GridInterface
     */
    public $grid;

    /**
     * @var string
     */
    public $message;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct(array_merge(['message' => 'lug.grid.sorting.invalid'], $options ?: []));
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['grid'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return GridSortingValidator::class;
    }
}
