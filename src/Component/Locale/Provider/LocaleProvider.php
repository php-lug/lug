<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Provider;

use Lug\Component\Locale\Exception\LocaleNotFoundException;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Resource\Repository\RepositoryInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var LocaleInterface[][]
     */
    private $cache = [];

    /**
     * @param RepositoryInterface $localeRepository
     * @param string              $defaultLocale
     */
    public function __construct(RepositoryInterface $localeRepository, $defaultLocale)
    {
        $this->localeRepository = $localeRepository;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        if (!isset($this->cache['default_locale'])) {
            $this->cache['default_locale'] = $this->localeRepository->findOneBy(
                $this->createCriteria(['code' => $code = $this->defaultLocale])
            );

            if ($this->cache['default_locale'] === null) {
                throw new LocaleNotFoundException(sprintf(
                    'The default locale "%s" could not be found.',
                    $code
                ));
            }
        }

        return $this->cache['default_locale'];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        if (!isset($this->cache['locales'])) {
            $this->cache['locales'] = $this->localeRepository->findBy($this->createCriteria());
        }

        return $this->cache['locales'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredLocales()
    {
        if (!isset($this->cache['required_locales'])) {
            $requiredLocales = [];

            foreach ($this->getLocales() as $locale) {
                if ($locale->isRequired()) {
                    $requiredLocales[] = $locale;
                }
            }

            $this->cache['required_locales'] = $requiredLocales;
        }

        return $this->cache['required_locales'];
    }

    /**
     * @param mixed[] $criteria
     *
     * @return mixed[]
     */
    private function createCriteria(array $criteria = [])
    {
        return array_merge(['enabled' => true], $criteria);
    }
}
