<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests\Resource;

use Lug\Bundle\LocaleBundle\Resource\LocaleResource;
use Lug\Bundle\ResourceBundle\Controller\Controller;
use Lug\Component\Locale\Form\Type\Doctrine\MongoDB\LocaleChoiceType as DoctrineMongoDBLocaleChoiceType;
use Lug\Component\Locale\Form\Type\Doctrine\ORM\LocaleChoiceType as DoctrineORMLocaleChoiceType;
use Lug\Component\Locale\Form\Type\LocaleType;
use Lug\Component\Locale\Model\Locale;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Locale\Resource\LocaleResource as LugLocaleResource;
use Lug\Component\Resource\Domain\DomainManager;
use Lug\Component\Resource\Factory\Factory;
use Lug\Component\Resource\Repository\Doctrine\MongoDB\Repository as DoctrineMongoDBRepository;
use Lug\Component\Resource\Repository\Doctrine\ORM\Repository as DoctrineORMRepository;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LocaleResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocaleResource
     */
    private $resource;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->resource = new LocaleResource();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LugLocaleResource::class, $this->resource);
    }

    public function testDefaultState()
    {
        $this->assertSame('locale', $this->resource->getName());
        $this->assertSame(LocaleResource::DRIVER_DOCTRINE_ORM, $this->resource->getDriver());
        $this->assertSame('default', $this->resource->getDriverManager());
        $this->assertSame(
            realpath(__DIR__.'/../../../../Component/Locale/Resources/Doctrine'),
            $this->resource->getDriverMappingPath()
        );
        $this->assertSame(LocaleResource::DRIVER_MAPPING_FORMAT_XML, $this->resource->getDriverMappingFormat());
        $this->assertSame([LocaleInterface::class], $this->resource->getInterfaces());
        $this->assertSame(Locale::class, $this->resource->getModel());
        $this->assertSame(Factory::class, $this->resource->getFactory());
        $this->assertSame(DoctrineORMRepository::class, $this->resource->getRepository());
        $this->assertSame(LocaleType::class, $this->resource->getForm());
        $this->assertSame(DoctrineORMLocaleChoiceType::class, $this->resource->getChoiceForm());
        $this->assertSame(Controller::class, $this->resource->getController());
        $this->assertSame(DomainManager::class, $this->resource->getDomainManager());
        $this->assertSame('code', $this->resource->getLabelPropertyPath());
        $this->assertSame('code', $this->resource->getIdPropertyPath());
        $this->assertEmpty($this->resource->getRelations());
    }

    public function testMongoDBDriver()
    {
        $this->resource = new LocaleResource($driver = LocaleResource::DRIVER_DOCTRINE_MONGODB);

        $this->assertSame('locale', $this->resource->getName());
        $this->assertSame($driver, $this->resource->getDriver());
        $this->assertSame(
            realpath(__DIR__.'/../../../../Component/Locale/Resources/Doctrine'),
            $this->resource->getDriverMappingPath()
        );
        $this->assertSame(LocaleResource::DRIVER_MAPPING_FORMAT_XML, $this->resource->getDriverMappingFormat());
        $this->assertSame('default', $this->resource->getDriverManager());
        $this->assertSame([LocaleInterface::class], $this->resource->getInterfaces());
        $this->assertSame(Locale::class, $this->resource->getModel());
        $this->assertSame(Factory::class, $this->resource->getFactory());
        $this->assertSame(DoctrineMongoDBRepository::class, $this->resource->getRepository());
        $this->assertSame(LocaleType::class, $this->resource->getForm());
        $this->assertSame(DoctrineMongoDBLocaleChoiceType::class, $this->resource->getChoiceForm());
        $this->assertSame(Controller::class, $this->resource->getController());
        $this->assertSame(DomainManager::class, $this->resource->getDomainManager());
        $this->assertSame('code', $this->resource->getLabelPropertyPath());
        $this->assertSame('code', $this->resource->getIdPropertyPath());
        $this->assertEmpty($this->resource->getRelations());
    }
}
