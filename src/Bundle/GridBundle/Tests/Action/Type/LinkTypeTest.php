<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\GridBundle\Tests\Action\Type;

use Lug\Bundle\GridBundle\Action\Type\LinkType;
use Lug\Component\Grid\Action\Type\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LinkTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinkType
     */
    private $type;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->urlGenerator = $this->createUrlGeneratorMock();
        $this->propertyAccessor = $this->createPropertyAccessorMock();

        $this->type = new LinkType($this->urlGenerator, $this->propertyAccessor);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(AbstractType::class, $this->type);
    }

    public function testRender()
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->identicalTo($route = 'route_value'),
                $this->identicalTo([
                    $routeParameter = 'route_parameter' => $routeParameterValue = 'route_parameter_value',
                ]),
                $this->identicalTo($routeReferenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
            )
            ->will($this->returnValue($url = 'url_value'));

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->identicalTo($data = new \stdClass()),
                $this->identicalTo($routeParameter)
            )
            ->will($this->returnValue($routeParameterValue));

        $this->assertSame($url, $this->type->render($data, [
            'route'                => $route,
            'route_parameters'     => [$routeParameter],
            'route_reference_type' => $routeReferenceType,
        ]));
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'route_parameters'     => [],
            'route_reference_type' => UrlGeneratorInterface::ABSOLUTE_PATH,
            'route'                => $route = 'route_value',
        ], $resolver->resolve(['route' => $route]));
    }

    public function testConfigureOptionsWithRouteParameters()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'route_parameters'     => $routeParameters = ['route_param'],
            'route_reference_type' => UrlGeneratorInterface::ABSOLUTE_PATH,
            'route'                => $route = 'route_value',
        ], $resolver->resolve([
            'route'            => $route,
            'route_parameters' => $routeParameters,
        ]));
    }

    public function testConfigureOptionsWithRouteReferenceType()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'route_parameters'     => [],
            'route_reference_type' => $routeReferenceType = UrlGeneratorInterface::ABSOLUTE_URL,
            'route'                => $route = 'route_value',
        ], $resolver->resolve([
            'route'                => $route,
            'route_reference_type' => $routeReferenceType,
        ]));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testConfigureOptionsWithMissingRoute()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidRoute()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['route' => true]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidRouteParameters()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['route' => 'route_value', 'route_parameters' => true]);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidRouteReferenceType()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve(['route' => 'route_value', 'route_reference_type' => 'foo']);
    }

    public function testName()
    {
        $this->assertSame('link', $this->type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|UrlGeneratorInterface
     */
    private function createUrlGeneratorMock()
    {
        return $this->getMock(UrlGeneratorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->getMock(PropertyAccessorInterface::class);
    }
}
