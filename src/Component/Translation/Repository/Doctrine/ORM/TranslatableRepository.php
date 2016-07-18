<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Translation\Repository\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Lug\Component\Resource\Model\ResourceInterface;
use Lug\Component\Resource\Repository\Doctrine\ORM\Repository;
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
     * @param EntityManager          $em
     * @param ClassMetadata          $class
     * @param ResourceInterface      $resource
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        $em,
        ClassMetadata $class,
        ResourceInterface $resource,
        LocaleContextInterface $localeContext
    ) {
        parent::__construct($em, $class, $resource);

        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder($alias = null, $indexBy = null)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy);
        $queryBuilder
            ->addSelect($alias = $this->getTranslationAlias($queryBuilder))
            ->leftJoin($this->getProperty('translations', $queryBuilder), $alias);

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderForCollection($alias = null, $indexBy = null)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy);
        $queryBuilder
            ->addSelect($alias = $this->getTranslationAlias($queryBuilder))
            ->innerJoin(
                $this->getProperty('translations', $queryBuilder),
                $alias,
                Join::WITH,
                $queryBuilder->expr()->in($this->getProperty('locale', $queryBuilder), $this->getLocales())
            );

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty($property, $root = null)
    {
        if ($this->cache === null) {
            $translationMetadata = $this
                ->getEntityManager()
                ->getClassMetadata($this->getClassMetadata()->getAssociationMapping('translations')['targetEntity']);

            $this->cache = array_diff(
                $this->getProperties($translationMetadata),
                $this->getProperties($this->getClassMetadata())
            );
        }

        if (in_array($this->getRootProperty($property), $this->cache, true)) {
            $root = $this->getTranslationAlias($root);
        }

        return parent::getProperty($property, $root);
    }

    /**
     * @param QueryBuilder|string|null $root
     *
     * @return string
     */
    private function getTranslationAlias($root)
    {
        return $this->getRootAlias($root).'_translation';
    }

    /**
     * @param QueryBuilder|string|null $root
     *
     * @return string
     */
    private function getRootAlias($root)
    {
        if ($root instanceof QueryBuilder) {
            $root = $root->getRootAliases()[0];
        }

        return (string) $root;
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
            array_keys($metadata->embeddedClasses)
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
