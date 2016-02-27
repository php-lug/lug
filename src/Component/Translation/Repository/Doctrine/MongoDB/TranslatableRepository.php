<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Repository\Doctrine\MongoDB;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\Doctrine\MongoDB\Repository;
use Lug\Component\Translation\Context\LocaleContextInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TranslatableRepository extends Repository
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var string[]
     */
    private $cache;

    /**
     * @param DocumentManager        $dm
     * @param UnitOfWork             $uow
     * @param ClassMetadata          $class
     * @param ResourceInterface      $resource
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        DocumentManager $dm,
        UnitOfWork $uow,
        ClassMetadata $class,
        ResourceInterface $resource,
        LocaleContextInterface $localeContext
    ) {
        parent::__construct($dm, $uow, $class, $resource);

        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderForCollection()
    {
        return parent::createQueryBuilderForCollection()->field($this->getProperty('locale'))->in($this->getLocales());
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($property, $root = null)
    {
        if ($this->cache === null) {
            $translatableFields = $this->getClassMetadata()->getFieldNames();
            $translationFields = $this->getDocumentManager()
                ->getClassMetadata($this->getClassMetadata()->getAssociationTargetClass($this->getTranslationAlias()))
                ->getFieldNames();

            $this->cache = array_diff($translationFields, $translatableFields);
        }

        if (in_array($property, $this->cache, true)) {
            $root = $this->getTranslationAlias();
        }

        return parent::getProperty($property, $root);
    }

    /**
     * @return string
     */
    private function getTranslationAlias()
    {
        return 'translations';
    }

    /**
     * @return string[]
     */
    private function getLocales()
    {
        $locales = $this->localeContext->getLocales();

        if (($fallbackLocale = $this->localeContext->getFallbackLocale()) !== null) {
            $locales[] = $fallbackLocale;
        }

        return $locales;
    }
}
