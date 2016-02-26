<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Locale\Resource;

use Lug\Bundle\ResourceBundle\Controller\Controller;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\MongoDB\Repository as DoctrineMongoDBRepository;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\Repository as DoctrineORMRepository;
use Lug\Component\Locale\Form\Type\Doctrine\MongoDB\LocaleChoiceType as DoctrineMongoDBLocaleChoiceType;
use Lug\Component\Locale\Form\Type\Doctrine\ORM\LocaleChoiceType as DoctrineORMLocaleChoiceType;
use Lug\Component\Locale\Form\Type\LocaleType;
use Lug\Component\Locale\Model\Locale;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Resource\Domain\DomainManager;
use Lug\Component\Resource\Factory\Factory;
use Lug\Component\Resource\Model\Resource;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleResource extends Resource
{
    /**
     * @param string $bundlePath
     * @param string $driver
     */
    public function __construct($bundlePath, $driver = self::DRIVER_DOCTRINE_ORM)
    {
        $orm = $driver === self::DRIVER_DOCTRINE_ORM;

        parent::__construct(
            'locale',
            $driver,
            'default',
            $bundlePath.'/Resources/config/resources',
            self::DRIVER_MAPPING_FORMAT_XML,
            LocaleInterface::class,
            Locale::class,
            Controller::class,
            Factory::class,
            $orm ? DoctrineORMRepository::class : DoctrineMongoDBRepository::class,
            DomainManager::class,
            LocaleType::class,
            $orm ? DoctrineORMLocaleChoiceType::class : DoctrineMongoDBLocaleChoiceType::class,
            'code',
            'code'
        );
    }
}
