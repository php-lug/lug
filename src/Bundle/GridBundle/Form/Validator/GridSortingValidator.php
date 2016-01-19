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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GridSortingValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        foreach ($value as $column => $order) {
            if (!$constraint->grid->hasSort($column)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('%column%', $column)
                    ->addViolation();
            }
        }
    }
}
