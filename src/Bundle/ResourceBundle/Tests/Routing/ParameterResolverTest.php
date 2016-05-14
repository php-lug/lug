<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Routing;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolver;
use Lug\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ParameterResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParameterResolver
     */
    private $parameterResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private $requestStack;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->requestStack = $this->createRequestStackMock();
        $this->propertyAccessor = $this->createPropertyAccessorMock();

        $this->parameterResolver = new ParameterResolver($this->requestStack, $this->propertyAccessor);
    }

    public function testResolveApiWithoutRequest()
    {
        $this->assertFalse($this->parameterResolver->resolveApi());
    }

    public function testResolveApiWihApiRequest()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_api'), $this->identicalTo(false))
            ->will($this->returnValue(true));

        $this->assertTrue($this->parameterResolver->resolveApi());
    }

    public function testResolveApiWithHtmlRequest()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request
            ->expects($this->once())
            ->method('getRequestFormat')
            ->will($this->returnValue('html'));

        $this->assertFalse($this->parameterResolver->resolveApi());
    }

    public function testResolveApiWithCustomRequest()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request
            ->expects($this->once())
            ->method('getRequestFormat')
            ->will($this->returnValue('json'));

        $this->assertTrue($this->parameterResolver->resolveApi());
    }

    public function testResolveCriteriaWithoutRequest()
    {
        $this->assertEmpty($this->parameterResolver->resolveCriteria());
    }

    public function testResolveCriteriaWithDefault()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_criteria'), $this->identicalTo([$identifier = 'id']))
            ->will($this->returnValue([$identifier]));

        $request
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($identifier), $this->isNull())
            ->will($this->returnValue($value = 'value'));

        $this->assertSame([$identifier => $value], $this->parameterResolver->resolveCriteria());
    }

    public function testResolveCriteriaWithExplicit()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_criteria'), $this->identicalTo(['id']))
            ->will($this->returnValue([$identifier = 'code']));

        $request
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($identifier), $this->isNull())
            ->will($this->returnValue($value = 'value'));

        $this->assertSame([$identifier => $value], $this->parameterResolver->resolveCriteria());
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException
     * @expectedExceptionMessage The request could not be found.
     */
    public function testResolveCriteriaMandatoryWithoutRequest()
    {
        $this->parameterResolver->resolveCriteria(true);
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RuntimeException
     * @expectedExceptionMessage The criteria could not be found for the route "route".
     */
    public function testResolveCriteriaMandatoryWithoutCriteria()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_criteria'), $this->identicalTo(['id']))
            ->will($this->returnValue([]));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_route'), $this->isNull())
            ->will($this->returnValue('route'));

        $this->parameterResolver->resolveCriteria(true);
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RuntimeException
     * @expectedExceptionMessage The criteria "id" could not be found for the route "route".
     */
    public function testResolveCriteriaMandatoryWithoutRequestCriteria()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_criteria'), $this->identicalTo([$identifier = 'id']))
            ->will($this->returnValue([$identifier]));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_route'), $this->isNull())
            ->will($this->returnValue('route'));

        $request
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($identifier), $this->isNull())
            ->will($this->returnValue(null));

        $this->parameterResolver->resolveCriteria(true);
    }

    public function testResolveCurrentPageWithoutRequest()
    {
        $this->assertSame(1, $this->parameterResolver->resolveCurrentPage());
    }

    public function testResolveCurrentPageDefault()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_page_parameter'), $this->identicalTo($pageParameter = 'page'))
            ->will($this->returnValue($pageParameter));

        $request
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($pageParameter), $this->identicalTo($page = 1))
            ->will($this->returnValue($page));

        $this->assertSame($page, $this->parameterResolver->resolveCurrentPage());
    }

    public function testResolveCurrentPageExplicitParameter()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_page_parameter'), $this->identicalTo('page'))
            ->will($this->returnValue($pageParameter = 'p'));

        $request
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($pageParameter), $this->identicalTo($page = 1))
            ->will($this->returnValue($page));

        $this->assertSame($page, $this->parameterResolver->resolveCurrentPage());
    }

    public function testResolveCurrentPageExplicitPage()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_page_parameter'), $this->identicalTo($pageParameter = 'page'))
            ->will($this->returnValue($pageParameter));

        $request
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo($pageParameter), $this->identicalTo(1))
            ->will($this->returnValue($page = 2));

        $this->assertSame($page, $this->parameterResolver->resolveCurrentPage());
    }

    public function testResolveFormWithoutRequest()
    {
        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = 'form'));

        $this->assertSame($form, $this->parameterResolver->resolveForm($resource));
    }

    public function testResolveFormDefault()
    {
        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = 'form'));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_form'), $this->identicalTo($form))
            ->will($this->returnValue($form));

        $this->assertSame($form, $this->parameterResolver->resolveForm($resource));
    }

    public function testResolveFormExplicit()
    {
        $resource = $this->createResourceMock();
        $resource
            ->expects($this->once())
            ->method('getForm')
            ->will($this->returnValue($form = 'form'));

        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_form'), $this->identicalTo($form))
            ->will($this->returnValue($explicitForm = 'explicit_form'));

        $this->assertSame($explicitForm, $this->parameterResolver->resolveForm($resource));
    }

    public function testResolveGrid()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_grid'), $this->identicalTo([]))
            ->will($this->returnValue($grid = ['foo' => 'bar']));

        $this->assertSame(
            array_merge(['resource' => $resource = $this->createResourceMock()], $grid),
            $this->parameterResolver->resolveGrid($resource)
        );
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException
     * @expectedExceptionMessage The request could not be found.
     */
    public function testResolveGridWithoutRequest()
    {
        $this->parameterResolver->resolveGrid($this->createResourceMock());
    }

    public function testResolveHateoasWithoutRequest()
    {
        $this->assertFalse($this->parameterResolver->resolveHateoas());
    }

    public function testResolveHateoasDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_hateoas'), $this->identicalTo($hateaos = false))
            ->will($this->returnValue($hateaos));

        $this->assertFalse($this->parameterResolver->resolveHateoas());
    }

    public function testResolveHateoasExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_hateoas'), $this->identicalTo(false))
            ->will($this->returnValue(true));

        $this->assertTrue($this->parameterResolver->resolveHateoas());
    }

    public function testResolveLocationRoute()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_location_route'), $this->isNull())
            ->will($this->returnValue($route = 'route'));

        $this->assertSame($route, $this->parameterResolver->resolveLocationRoute());
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException
     * @expectedExceptionMessage The request could not be found.
     */
    public function testResolveLocationRouteWithoutRequest()
    {
        $this->parameterResolver->resolveLocationRoute();
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RuntimeException
     * @expectedExceptionMessage The location route could not be found for the route "route".
     */
    public function testResolveLocationRouteMissing()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_location_route'), $this->isNull())
            ->will($this->returnValue(null));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_route'), $this->isNull())
            ->will($this->returnValue('route'));

        $this->parameterResolver->resolveLocationRoute();
    }

    public function testResolveLocationRouteParametersWithoutRequest()
    {
        $this->assertEmpty($this->parameterResolver->resolveLocationRouteParameters(new \stdClass()));
    }

    public function testResolveLocationRouteParametersDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_location_route_parameters'), $this->identicalTo($parameters = []))
            ->will($this->returnValue($parameters));

        $this->assertEmpty($this->parameterResolver->resolveLocationRouteParameters(new \stdClass()));
    }

    public function testResolveLocationRouteParametersExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_location_route_parameters'), $this->identicalTo([]))
            ->will($this->returnValue([$parameter = 'id']));

        $object = new \stdClass();
        $object->{$parameter} = $value = 1;

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with($this->identicalTo($object), $this->identicalTo($parameter))
            ->will($this->returnValue($value));

        $this->assertSame([$parameter => $value], $this->parameterResolver->resolveLocationRouteParameters($object));
    }

    public function testResolveMaxPerPageWithoutRequest()
    {
        $this->assertSame(10, $this->parameterResolver->resolveMaxPerPage());
    }

    public function testResolveMaxPerPageDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_max_per_page'), $this->identicalTo($maxPerPage = 10))
            ->will($this->returnValue($maxPerPage));

        $this->assertSame($maxPerPage, $this->parameterResolver->resolveMaxPerPage());
    }

    public function testResolveMaxPerPageExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_max_per_page'), $this->identicalTo(10))
            ->will($this->returnValue($maxPerPage = 5));

        $this->assertSame($maxPerPage, $this->parameterResolver->resolveMaxPerPage());
    }

    public function testResolveRedirectRoute()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route'), $this->isNull())
            ->will($this->returnValue($route = 'route'));

        $this->assertSame($route, $this->parameterResolver->resolveRedirectRoute());
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException
     * @expectedExceptionMessage The request could not be found.
     */
    public function testResolveRedirectRouteWithoutRequest()
    {
        $this->parameterResolver->resolveRedirectRoute();
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RuntimeException
     * @expectedExceptionMessage The redirect route could not be found for the route "route".
     */
    public function testResolveRedirectRouteMissing()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route'), $this->isNull())
            ->will($this->returnValue(null));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_route'), $this->isNull())
            ->will($this->returnValue('route'));

        $this->parameterResolver->resolveRedirectRoute();
    }

    public function testResolveRedirectRouteParametersWithoutRequest()
    {
        $this->assertEmpty($this->parameterResolver->resolveRedirectRouteParameters(new \stdClass()));
    }

    public function testResolveRedirectRouteParametersDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route_parameters'), $this->identicalTo($parameters = []))
            ->will($this->returnValue($parameters));

        $this->assertEmpty($this->parameterResolver->resolveRedirectRouteParameters(new \stdClass()));
    }

    public function testResolveRedirectRouteParametersExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route_parameters'), $this->identicalTo([]))
            ->will($this->returnValue([$parameter = 'id']));

        $object = new \stdClass();
        $object->{$parameter} = $value = 1;

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with($this->identicalTo($object), $this->identicalTo($parameter))
            ->will($this->returnValue($value));

        $this->assertSame([$parameter => $value], $this->parameterResolver->resolveRedirectRouteParameters($object));
    }

    public function testResolveRedirectRouteParametersForwardParameters()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route_parameters'), $this->identicalTo([]))
            ->will($this->returnValue([$parameter = 'id']));

        $request->query = $this->createParameterBagMock();
        $request->query
            ->expects($this->once())
            ->method('all')
            ->will($this->returnValue($query = ['foo' => 'bar']));

        $object = new \stdClass();
        $object->{$parameter} = $value = 1;

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with($this->identicalTo($object), $this->identicalTo($parameter))
            ->will($this->returnValue($value));

        $this->assertSame(
            array_merge($query, [$parameter => $value]),
            $this->parameterResolver->resolveRedirectRouteParameters($object, true)
        );
    }

    public function testResolveRedirectRouteParametersWithoutObject()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route_parameters'), $this->identicalTo([]))
            ->will($this->returnValue([]));

        $this->assertEmpty($this->parameterResolver->resolveRedirectRouteParameters());
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RuntimeException
     * @expectedExceptionMessage The route parameters could not be found for the route "redirect_route_parameters".
     */
    public function testResolveRedirectRouteParametersWithObjectMissing()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route_parameters'), $this->identicalTo([]))
            ->will($this->returnValue(['id']));

        $this->assertEmpty($this->parameterResolver->resolveRedirectRouteParameters());
    }

    public function testResolveRedirectRouteParametersForwardWithoutRequest()
    {
        $this->assertFalse($this->parameterResolver->resolveRedirectRouteParametersForward());
    }

    public function testResolveRedirectRouteParametersForwardDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route_parameters_forward'), $this->identicalTo($forward = false))
            ->will($this->returnValue($forward));

        $this->assertFalse($this->parameterResolver->resolveRedirectRouteParametersForward());
    }

    public function testResolveRedirectRouteParametersForwardExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_redirect_route_parameters_forward'), $this->identicalTo(false))
            ->will($this->returnValue(true));

        $this->assertTrue($this->parameterResolver->resolveRedirectRouteParametersForward());
    }

    public function testResolveRepositoryMethodWithoutRequest()
    {
        $this->assertSame('findForTest', $this->parameterResolver->resolveRepositoryMethod('test'));
    }

    public function testResolveRepositoryMethodDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_repository_method'), $this->identicalTo($repositoryMethod = 'findForTest'))
            ->will($this->returnValue($repositoryMethod));

        $this->assertSame($repositoryMethod, $this->parameterResolver->resolveRepositoryMethod('test'));
    }

    public function testResolveRepositoryMethodExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_repository_method'), $this->identicalTo('findForTest'))
            ->will($this->returnValue($repositoryMethod = 'findForAction'));

        $this->assertSame($repositoryMethod, $this->parameterResolver->resolveRepositoryMethod('test'));
    }

    public function testResolveSerializerGroupsWithoutRequest()
    {
        $this->assertEmpty($this->parameterResolver->resolveSerializerGroups());
    }

    public function testResolveSerializerGroupsDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_serializer_groups'), $this->identicalTo($groups = []))
            ->will($this->returnValue($groups));

        $this->assertEmpty($this->parameterResolver->resolveSerializerGroups());
    }

    public function testResolveSerializerGroupsExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_serializer_groups'), $this->identicalTo([]))
            ->will($this->returnValue($groups = ['group']));

        $this->assertSame($groups, $this->parameterResolver->resolveSerializerGroups());
    }

    public function testResolveSerializerNullWithoutRequest()
    {
        $this->assertTrue($this->parameterResolver->resolveSerializerNull());
    }

    public function testResolveSerializerNullDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_serializer_null'), $this->identicalTo($null = true))
            ->will($this->returnValue($null));

        $this->assertTrue($this->parameterResolver->resolveSerializerNull());
    }

    public function testResolveSerializerNullExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_serializer_null'), $this->identicalTo(true))
            ->will($this->returnValue(false));

        $this->assertFalse($this->parameterResolver->resolveSerializerNull());
    }

    public function testResolveSortingWithoutRequest()
    {
        $this->assertEmpty($this->parameterResolver->resolveSorting());
    }

    public function testResolveSortingDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_sorting'), $this->identicalTo($sorting = []))
            ->will($this->returnValue($sorting));

        $this->assertEmpty($this->parameterResolver->resolveSorting());
    }

    public function testResolveSortingExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_sorting'), $this->identicalTo([]))
            ->will($this->returnValue($sorting = ['foo' => 'ASC']));

        $this->assertSame($sorting, $this->parameterResolver->resolveSorting());
    }

    public function testResolveTemplate()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_template'), $this->isNull())
            ->will($this->returnValue($template = 'template'));

        $this->assertSame($template, $this->parameterResolver->resolveTemplate());
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RequestNotFoundException
     * @expectedExceptionMessage The request could not be found.
     */
    public function testResolveTemplateWithoutRequest()
    {
        $this->parameterResolver->resolveTemplate();
    }

    /**
     * @expectedException \Lug\Bundle\ResourceBundle\Exception\RuntimeException
     * @expectedExceptionMessage The template could not be found for the route "route".
     */
    public function testResolveTemplateMissing()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_template'), $this->isNull())
            ->will($this->returnValue(null));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_route'), $this->isNull())
            ->will($this->returnValue('route'));

        $this->parameterResolver->resolveTemplate();
    }

    public function testResolveThemesWithoutRequest()
    {
        $this->assertEmpty($this->parameterResolver->resolveThemes());
    }

    public function testResolveThemesDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_themes'), $this->identicalTo($themes = []))
            ->will($this->returnValue($themes));

        $this->assertEmpty($this->parameterResolver->resolveThemes());
    }

    public function testResolveThemesExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_themes'), $this->identicalTo([]))
            ->will($this->returnValue($themes = ['theme']));

        $this->assertSame($themes, $this->parameterResolver->resolveThemes());
    }

    public function testResolveTranslationDomainWithoutRequest()
    {
        $this->assertSame('forms', $this->parameterResolver->resolveTranslationDomain());
    }

    public function testResolveTranslationDomainDefault()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_grid'), $this->isNull())
            ->will($this->returnValue(null));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_lug_translation_domain'), $this->identicalTo($translationDomain = 'forms'))
            ->will($this->returnValue($translationDomain));

        $this->assertSame($translationDomain, $this->parameterResolver->resolveTranslationDomain());
    }

    public function testResolveTranslationDomainExplicit()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_grid'), $this->isNull())
            ->will($this->returnValue(null));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_lug_translation_domain'), $this->identicalTo('forms'))
            ->will($this->returnValue($translationDomain = 'translation_domain'));

        $this->assertSame($translationDomain, $this->parameterResolver->resolveTranslationDomain());
    }

    public function testResolveTranslationDomainWithGrid()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_grid'), $this->isNull())
            ->will($this->returnValue(['grid']));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_lug_translation_domain'), $this->identicalTo($translationDomain = 'grids'))
            ->will($this->returnValue($translationDomain));

        $this->assertSame($translationDomain, $this->parameterResolver->resolveTranslationDomain());
    }

    public function testResolveTranslationDomainExplicitWithGrid()
    {
        $this->requestStack
            ->expects($this->exactly(2))
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->at(0))
            ->method('get')
            ->with($this->identicalTo('_lug_grid'), $this->isNull())
            ->will($this->returnValue(['grid']));

        $request->attributes
            ->expects($this->at(1))
            ->method('get')
            ->with($this->identicalTo('_lug_translation_domain'), $this->identicalTo('grids'))
            ->will($this->returnValue($translationDomain = 'translation_domain'));

        $this->assertSame($translationDomain, $this->parameterResolver->resolveTranslationDomain());
    }

    public function testResolveValidationGroupsWithoutRequest()
    {
        $this->assertEmpty($this->parameterResolver->resolveValidationGroups());
    }

    public function testResolveValidationGroupsDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_validation_groups'), $this->identicalTo($groups = []))
            ->will($this->returnValue($groups));

        $this->assertEmpty($this->parameterResolver->resolveValidationGroups());
    }

    public function testResolveValidationGroupsExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_validation_groups'), $this->identicalTo([]))
            ->will($this->returnValue($groups = ['group']));

        $this->assertSame($groups, $this->parameterResolver->resolveValidationGroups());
    }

    public function testResolveVoterWithoutRequest()
    {
        $this->assertFalse($this->parameterResolver->resolveVoter());
    }

    public function testResolveVoterDefault()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_voter'), $this->identicalTo(false))
            ->will($this->returnValue(false));

        $this->assertFalse($this->parameterResolver->resolveVoter());
    }

    public function testResolveVoterExplicit()
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getMasterRequest')
            ->will($this->returnValue($request = $this->createRequestMock()));

        $request->attributes
            ->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('_lug_voter'), $this->identicalTo(false))
            ->will($this->returnValue(true));

        $this->assertTrue($this->parameterResolver->resolveVoter());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestStack
     */
    private function createRequestStackMock()
    {
        return $this->getMock(RequestStack::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|PropertyAccessorInterface
     */
    private function createPropertyAccessorMock()
    {
        return $this->getMock(PropertyAccessorInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private function createRequestMock()
    {
        $request = $this->getMock(Request::class);
        $request->attributes = $this->createParameterBagMock();

        return $request;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterBag
     */
    private function createParameterBagMock()
    {
        return $this->getMock(ParameterBag::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
