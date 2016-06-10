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

use Lug\Bundle\GridBundle\Action\Type\ActionType;
use Lug\Bundle\GridBundle\View\GridViewInterface;
use Lug\Component\Grid\Action\Type\ActionType as BaseActionType;
use Lug\Component\Grid\Model\ActionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ActionTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionType
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->type = new ActionType();
    }

    public function testInheritance()
    {
        $this->assertInstanceOf(BaseActionType::class, $this->type);
    }

    public function testConfigureOptions()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame([
            'trans_domain' => 'grids',
            'action'       => $action = $this->createActionMock(),
            'grid'         => $grid = $this->createGridViewMock(),
        ], $resolver->resolve([
            'action' => $action,
            'grid'   => $grid,
        ]));
    }

    public function testConfigureOptionsWithTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $this->assertSame($options = [
            'trans_domain' => 'domain',
            'action'       => $this->createActionMock(),
            'grid'         => $this->createGridViewMock(),
        ], $resolver->resolve($options));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testConfigureOptionsWithInvalidTransDomain()
    {
        $this->type->configureOptions($resolver = new OptionsResolver());

        $resolver->resolve([
            'trans_domain' => true,
            'action'       => $this->createActionMock(),
            'grid'         => $this->createGridViewMock(),
        ]);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|ActionInterface
     */
    private function createActionMock()
    {
        return $this->createMock(ActionInterface::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GridViewInterface
     */
    private function createGridViewMock()
    {
        return $this->createMock(GridViewInterface::class);
    }
}
