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

use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleIntegrityValidator extends ConstraintValidator
{
    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(LocaleProviderInterface $localeProvider)
    {
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($locale, Constraint $constraint)
    {
        if ($locale->isEnabled() && $locale->isRequired()) {
            return;
        }

        $this->validateDefaultLocale($locale, $constraint);
        $this->validateLastLocale($locale, $constraint);
    }

    /**
     * @param LocaleInterface $locale
     * @param LocaleIntegrity $constraint
     */
    private function validateDefaultLocale(LocaleInterface $locale, LocaleIntegrity $constraint)
    {
        if ($this->localeProvider->getDefaultLocale() === $locale) {
            if (!$locale->isEnabled()) {
                $this->context->buildViolation($constraint->defaultEnabledMessage)
                    ->atPath('enabled')
                    ->addViolation();
            }

            if (!$locale->isRequired()) {
                $this->context->buildViolation($constraint->defaultRequiredMessage)
                    ->atPath('required')
                    ->addViolation();
            }
        }
    }

    /**
     * @param LocaleInterface $locale
     * @param LocaleIntegrity $constraint
     */
    private function validateLastLocale(LocaleInterface $locale, LocaleIntegrity $constraint)
    {
        $locales = $this->localeProvider->getRequiredLocales();

        if (count($locales) === 1 && reset($locales) === $locale) {
            if (!$locale->isEnabled()) {
                $this->context->buildViolation($constraint->lastEnabledMessage)
                    ->atPath('enabled')
                    ->addViolation();
            }

            if (!$locale->isRequired()) {
                $this->context->buildViolation($constraint->lastRequiredMessage)
                    ->atPath('required')
                    ->addViolation();
            }
        }
    }
}
