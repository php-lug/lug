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

use Lug\Bundle\GridBundle\Action\Type\LinkSecureType;
use Lug\Bundle\GridBundle\Action\Type\LinkType;
use Lug\Bundle\ResourceBundle\Form\Type\CsrfProtectionType;
use Lug\Component\Grid\Model\ActionInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class LinkSecureTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinkSecureType
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
     * @var \PHPUnit_Framework_MockObject_MockObject|FormFactoryInterface
     */
    private $formFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->urlGenerator = $this->createUrlGeneratorMock();
        $this->propertyAccessor = $this->createPropertyAccessorMock();
        $this->formFactory = $this->createFormFactoryMock();

        $this->type = new LinkSecureType($this->urlGenerator, $this->propertyAccessor, $this->formFactory);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(LinkType::class, $this->type);
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

        $action = $this->createActionMock();
        $action
            ->expects($this->once())
            ->method('getLabel')
            ->will($this->returnValue($actionLabel = 'action_label'));

        $action
            ->expects($this->once())
            ->method('getOption')
            ->with($this->identicalTo('trans_domain'))
            ->will($this->returnValue($actionTransDomain = 'action_trans_domain'));

        $this->formFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->identicalTo(CsrfProtectionType::class),
                $this->isNull(),
                $this->identicalTo([
                    'method'             => $method = Request::METHOD_DELETE,
                    'action'             => $url,
                    'label'              => $actionLabel,
                    'translation_domain' => $actionTransDomain,
                ])
            )
            ->will($this->returnValue($form = $this->createFormMock()));

        $form
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($view = $this->createFormViewMock()));

        $this->assertSame($view, $this->type->render($data, [
            'action'               => $action,
            'method'               => strtolower($method),
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
            'method'               => Request::METHOD_GET,
            'route'                => $route = 'route_value',
        ], $resolver->resolve(['route' => $route]));
    }

    public function testConfigureOptionsWithMethod()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'route_parameters'     => [],
            'route_reference_type' => UrlGeneratorInterface::ABSOLUTE_PATH,
            'method'               => $method = Request::METHOD_DELETE,
            'route'                => $route = 'route_value',
        ], $resolver->resolve([
            'route'  => $route,
            'method' => $method,
        ]));
    }

    public function testName()
    {
        $this->assertSame('link_secure', $this->type->getName());
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormFactoryInterface
     */
    private function createFormFactoryMock()
    {
        return $this->getMock(FormFactoryInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function createActionMock()
    {
        return $this->getMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormInterface
     */
    private function createFormMock()
    {
        return $this->getMock(FormInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|FormView
     */
    private function createFormViewMock()
    {
        return $this->getMock(FormView::class);
    }
}
