<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @author GeLo <geloen.eric@gmail.com>
 *
 * @Annotation
 */
class LocaleIntegrity extends Constraint
{
    /**
     * @var string
     */
    public $defaultEnabledMessage = 'The locale is the default one. You can\'t disable it.';

    /**
     * @var string
     */
    public $defaultRequiredMessage = 'The locale is the default one. You can\'t make it optional.';

    /**
     * @var string
     */
    public $lastEnabledMessage = 'The locale is the last enabled. You can\'t disable it.';

    /**
     * @var string
     */
    public $lastRequiredMessage = 'The locale is the last required. You can\'t make it optional.';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'lug_locale_integrity';
    }
}
