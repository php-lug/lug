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

use Lug\Bundle\LocaleBundle\Form\Type\Doctrine\MongoDB\LocaleChoiceType as DoctrineMongoDBLocaleChoiceType;
use Lug\Bundle\LocaleBundle\Form\Type\Doctrine\ORM\LocaleChoiceType as DoctrineORMLocaleChoiceType;
use Lug\Bundle\LocaleBundle\Form\Type\LocaleType;
use Lug\Bundle\LocaleBundle\Resource\LocaleResource;
use Lug\Bundle\ResourceBundle\Controller\Controller;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\MongoDB\Repository as DoctrineMongoDBRepository;
use Lug\Bundle\ResourceBundle\Repository\Doctrine\ORM\Repository as DoctrineORMRepository;
use Lug\Component\Locale\Model\Locale;
use Lug\Component\Locale\Model\LocaleInterface;
use Lug\Component\Resource\Domain\DomainManager;
use Lug\Component\Resource\Factory\Factory;
use Lug\Component\Resource\Model\Resource;

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
     * @var string
     */
    private $bundlePath;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundlePath = realpath(__DIR__.'/../../');
        $this->resource = new LocaleResource($this->bundlePath);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(Resource::class, $this->resource);
    }

    public function testDefaultState()
    {
        $this->assertSame('locale', $this->resource->getName());
        $this->assertSame(LocaleResource::DRIVER_DOCTRINE_ORM, $this->resource->getDriver());
        $this->assertSame('default', $this->resource->getDriverManager());
        $this->assertSame($this->bundlePath.'/Resources/config/resources', $this->resource->getDriverMappingPath());
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
        $this->assertNull($this->resource->getTranslation());
    }

    public function testMongoDBDriver()
    {
        $this->resource = new LocaleResource($this->bundlePath, $driver = LocaleResource::DRIVER_DOCTRINE_MONGODB);

        $this->assertSame('locale', $this->resource->getName());
        $this->assertSame($driver, $this->resource->getDriver());
        $this->assertSame($this->bundlePath.'/Resources/config/resources', $this->resource->getDriverMappingPath());
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
        $this->assertNull($this->resource->getTranslation());
    }
}
