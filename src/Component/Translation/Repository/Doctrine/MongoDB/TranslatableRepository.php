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
            $translationMetadata = $this
                ->getDocumentManager()
                ->getClassMetadata($this->getClassMetadata()->getAssociationTargetClass($this->getTranslationAlias()));

            $this->cache = array_diff(
                $this->getProperties($translationMetadata),
                $this->getProperties($this->getClassMetadata())
            );
        }

        if (in_array($this->getRootProperty($property), $this->cache, true)) {
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
     * @param ClassMetadata $metadata
     *
     * @return string[]
     */
    private function getProperties(ClassMetadata $metadata)
    {
        return array_merge(
            $metadata->getFieldNames(),
            array_map(function (array $mapping) {
                return $mapping['fieldName'];
            }, $metadata->getEmbeddedFieldsMappings())
        );
    }

    /**
     * @param string $property
     *
     * @return string
     */
    private function getRootProperty($property)
    {
        return ($pos = strpos($property, '.')) !== false ? substr($property, 0, $pos) : $property;
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
