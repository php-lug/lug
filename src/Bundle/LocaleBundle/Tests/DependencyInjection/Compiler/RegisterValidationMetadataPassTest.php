<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\LocaleBundle\Tests\DependencyInjection\Compiler;

use Lug\Bundle\LocaleBundle\DependencyInjection\Compiler\RegisterValidationMetadataPass;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterValidationMetadataPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegisterValidationMetadataPass
     */
    private $compiler;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compiler = new RegisterValidationMetadataPass();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(CompilerPassInterface::class, $this->compiler);
    }

    /**
     * @dataProvider processProvider
     */
    public function testProcess($driver, $mapping)
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->exactly(2))
            ->method('getDefinition')
            ->will($this->returnValueMap([
                ['validator.builder', $validatorBuilder = $this->createDefinitionMock()],
                ['lug.resource.locale', $localeResource = $this->createDefinitionMock()],
            ]));

        $localeResource
            ->expects($this->once())
            ->method('getArgument')
            ->with($this->identicalTo(1))
            ->will($this->returnValue($driver));

        $validatorBuilder
            ->expects($this->once())
            ->method('addMethodCall')
            ->with(
                $this->identicalTo('addXmlMapping'),
                $this->identicalTo([realpath(__DIR__.'/../../../Resources/config/validator/'.$mapping)])
            );

        $this->compiler->process($container);
    }

    public function testProcessWithMongoDBDriver()
    {
        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->exactly(2))
            ->method('getDefinition')
            ->will($this->returnValueMap([
                ['validator.builder', $validatorBuilder = $this->createDefinitionMock()],
                ['lug.resource.locale', $localeResource = $this->createDefinitionMock()],
            ]));

        $localeResource
            ->expects($this->once())
            ->method('getArgument')
            ->with($this->identicalTo(1))
            ->will($this->returnValue('foo'));

        $validatorBuilder
            ->expects($this->never())
            ->method('addMethodCall');

        $this->compiler->process($container);
    }

    /**
     * @return mixed(]
     */
    public function processProvider()
    {
        return [
            [ResourceInterface::DRIVER_DOCTRINE_ORM, 'Locale.orm.xml'],
            [ResourceInterface::DRIVER_DOCTRINE_MONGODB, 'Locale.mongodb.xml'],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ContainerBuilder
     */
    private function createContainerBuilderMock()
    {
        return $this->createMock(ContainerBuilder::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Definition
     */
    private function createDefinitionMock()
    {
        return $this->createMock(Definition::class);
    }
}
