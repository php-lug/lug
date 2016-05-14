<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Tests\Security;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Bundle\ResourceBundle\Security\SecurityChecker;
use Lug\Bundle\ResourceBundle\Security\SecurityCheckerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SecurityCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SecurityChecker
     */
    private $securityChecker;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->authorizationChecker = $this->createAuthorizationCheckerMock();
        $this->parameterResolver = $this->createParameterResolverMock();

        $this->securityChecker = new SecurityChecker($this->authorizationChecker, $this->parameterResolver);
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(SecurityCheckerInterface::class, $this->securityChecker);
    }

    public function testIsGrantedWithoutVoter()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveVoter')
            ->will($this->returnValue(false));

        $this->authorizationChecker
            ->expects($this->never())
            ->method('isGranted');

        $this->assertTrue($this->securityChecker->isGranted('show', $this->createStdClassMock()));
    }

    public function testIsGrantedWithVoter()
    {
        $this->parameterResolver
            ->expects($this->once())
            ->method('resolveVoter')
            ->will($this->returnValue(true));

        $this->authorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with(
                $this->identicalTo('lug.'.($action = 'show')),
                $this->identicalTo($object = $this->createStdClassMock())
            )
            ->will($this->returnValue(true));

        $this->assertTrue($this->securityChecker->isGranted($action, $object));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|AuthorizationCheckerInterface
     */
    private function createAuthorizationCheckerMock()
    {
        return $this->getMock(AuthorizationCheckerInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ParameterResolverInterface
     */
    private function createParameterResolverMock()
    {
        return $this->getMock(ParameterResolverInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\stdClass
     */
    private function createStdClassMock()
    {
        return $this->getMock(\stdClass::class);
    }
}
