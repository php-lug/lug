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

use Lug\Bundle\ResourceBundle\Routing\CachedParameterResolver;
use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Model\ResourceInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CachedParameterResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CachedParameterResolver
     */
    private $cachedParameterResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->parameterResolver = $this->createParameterResolverMock();
        $this->cachedParameterResolver = new CachedParameterResolver($this->parameterResolver);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(ParameterResolverInterface::class, $this->cachedParameterResolver);
    }

    public function testResolveApi()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveApi')
            ->will($this->returnValue(true));

        $this->assertTrue($this->cachedParameterResolver->resolveApi());
        $this->assertTrue($this->cachedParameterResolver->resolveApi());
    }

    public function testResolveCriteria()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveCriteria')
            ->with($this->identicalTo($mandatory = false))
            ->will($this->returnValue($criteria = ['criteria']));

        $this->assertSame($criteria, $this->cachedParameterResolver->resolveCriteria($mandatory));
        $this->assertSame($criteria, $this->cachedParameterResolver->resolveCriteria($mandatory));
    }

    public function testResolveCriteriaMissing()
    {
        $this->parameterResolver
            ->expects($this->at(0))
            ->method('resolveCriteria')
            ->with($this->identicalTo($firstMandatory = false))
            ->will($this->returnValue($criteria = []));

        $this->parameterResolver
            ->expects($this->at(1))
            ->method('resolveCriteria')
            ->with($this->identicalTo($secondMandatory = true))
            ->will($this->throwException($exception = new \Exception()));

        $this->assertSame($criteria, $this->cachedParameterResolver->resolveCriteria($firstMandatory));

        try {
            $this->cachedParameterResolver->resolveCriteria($secondMandatory);
        } catch (\Exception $e) {
            $this->assertSame($exception, $e);
        }
    }

    public function testResolveCurrentPage()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveCurrentPage')
            ->will($this->returnValue($currentPage = 2));

        $this->assertSame($currentPage, $this->cachedParameterResolver->resolveCurrentPage());
        $this->assertSame($currentPage, $this->cachedParameterResolver->resolveCurrentPage());
    }

    public function testResolveForm()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveForm')
            ->with($this->identicalTo($resource = $this->createResourceMock()))
            ->will($this->returnValue($form = 'form'));

        $this->assertSame($form, $this->cachedParameterResolver->resolveForm($resource));
        $this->assertSame($form, $this->cachedParameterResolver->resolveForm($resource));
    }

    public function testResolveGrid()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveGrid')
            ->with($this->identicalTo($resource = $this->createResourceMock()))
            ->will($this->returnValue($grid = 'grid'));

        $this->assertSame($grid, $this->cachedParameterResolver->resolveGrid($resource));
        $this->assertSame($grid, $this->cachedParameterResolver->resolveGrid($resource));
    }

    public function testResolveHateoas()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveHateoas')
            ->will($this->returnValue(true));

        $this->assertTrue($this->cachedParameterResolver->resolveHateoas());
        $this->assertTrue($this->cachedParameterResolver->resolveHateoas());
    }

    public function testResolveLocationRoute()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveLocationRoute')
            ->will($this->returnValue($locationRoute = 'location_route'));

        $this->assertSame($locationRoute, $this->cachedParameterResolver->resolveLocationRoute());
        $this->assertSame($locationRoute, $this->cachedParameterResolver->resolveLocationRoute());
    }

    public function testResolveLocationRouteParameters()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveLocationRouteParameters')
            ->with($this->identicalTo($object = new \stdClass()))
            ->will($this->returnValue($locationRouteParameters = ['location_route_param']));

        $this->assertSame(
            $locationRouteParameters,
            $this->cachedParameterResolver->resolveLocationRouteParameters($object)
        );

        $this->assertSame(
            $locationRouteParameters,
            $this->cachedParameterResolver->resolveLocationRouteParameters($object)
        );
    }

    public function testResolveMaxPerPage()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveMaxPerPage')
            ->will($this->returnValue($maxPerPage = 5));

        $this->assertSame($maxPerPage, $this->cachedParameterResolver->resolveMaxPerPage());
        $this->assertSame($maxPerPage, $this->cachedParameterResolver->resolveMaxPerPage());
    }

    public function testResolveRedirectRoute()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRedirectRoute')
            ->will($this->returnValue($redirectRoute = 'redirect_route'));

        $this->assertSame($redirectRoute, $this->cachedParameterResolver->resolveRedirectRoute());
        $this->assertSame($redirectRoute, $this->cachedParameterResolver->resolveRedirectRoute());
    }

    public function testResolveRedirectRouteParameters()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRedirectRouteParameters')
            ->with(
                $this->identicalTo($object = new \stdClass()),
                $this->identicalTo($forwardParameters = true)
            )
            ->will($this->returnValue($redirectRouteParameters = ['redirect_route_param']));

        $this->assertSame(
            $redirectRouteParameters,
            $this->cachedParameterResolver->resolveRedirectRouteParameters($object, $forwardParameters)
        );

        $this->assertSame(
            $redirectRouteParameters,
            $this->cachedParameterResolver->resolveRedirectRouteParameters($object, $forwardParameters)
        );
    }

    public function testResolveRedirectRouteParametersForward()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRedirectRouteParametersForward')
            ->will($this->returnValue(true));

        $this->assertTrue($this->cachedParameterResolver->resolveRedirectRouteParametersForward());
        $this->assertTrue($this->cachedParameterResolver->resolveRedirectRouteParametersForward());
    }

    public function testResolveRepositoryMethod()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveRepositoryMethod')
            ->with($this->identicalTo($action = 'action'))
            ->will($this->returnValue($repositoryMethod = 'repository_method'));

        $this->assertSame($repositoryMethod, $this->cachedParameterResolver->resolveRepositoryMethod($action));
        $this->assertSame($repositoryMethod, $this->cachedParameterResolver->resolveRepositoryMethod($action));
    }

    public function testResolveSerializerGroups()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveSerializerGroups')
            ->will($this->returnValue($groups = ['group']));

        $this->assertSame($groups, $this->cachedParameterResolver->resolveSerializerGroups());
        $this->assertSame($groups, $this->cachedParameterResolver->resolveSerializerGroups());
    }

    public function testResolveSerializerNull()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveSerializerNull')
            ->will($this->returnValue(true));

        $this->assertTrue($this->cachedParameterResolver->resolveSerializerNull());
        $this->assertTrue($this->cachedParameterResolver->resolveSerializerNull());
    }

    public function testResolveSorting()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveSorting')
            ->will($this->returnValue($sorting = ['sorting']));

        $this->assertSame($sorting, $this->cachedParameterResolver->resolveSorting());
        $this->assertSame($sorting, $this->cachedParameterResolver->resolveSorting());
    }

    public function testResolveTemplate()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveTemplate')
            ->will($this->returnValue($template = 'template'));

        $this->assertSame($template, $this->cachedParameterResolver->resolveTemplate());
        $this->assertSame($template, $this->cachedParameterResolver->resolveTemplate());
    }

    public function testResolveThemes()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveThemes')
            ->will($this->returnValue($themes = ['theme']));

        $this->assertSame($themes, $this->cachedParameterResolver->resolveThemes());
        $this->assertSame($themes, $this->cachedParameterResolver->resolveThemes());
    }

    public function testResolveTranslationDomain()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveTranslationDomain')
            ->will($this->returnValue($translationDomain = 'translation_domain'));

        $this->assertSame($translationDomain, $this->cachedParameterResolver->resolveTranslationDomain());
        $this->assertSame($translationDomain, $this->cachedParameterResolver->resolveTranslationDomain());
    }

    public function testResolveValidationGroups()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveValidationGroups')
            ->with($this->identicalTo($resource = $this->createResourceMock()))
            ->will($this->returnValue($groups = ['group']));

        $this->assertSame($groups, $this->cachedParameterResolver->resolveValidationGroups($resource));
        $this->assertSame($groups, $this->cachedParameterResolver->resolveValidationGroups($resource));
    }

    public function testResolveVoter()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveVoter')
            ->will($this->returnValue(true));

        $this->assertTrue($this->cachedParameterResolver->resolveVoter());
        $this->assertTrue($this->cachedParameterResolver->resolveVoter());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ResourceInterface
     */
    private function createResourceMock()
    {
        return $this->getMock(ResourceInterface::class);
    }
}
