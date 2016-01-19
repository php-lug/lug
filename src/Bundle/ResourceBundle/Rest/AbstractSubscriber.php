<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Rest;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(ParameterResolverInterface $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @return ParameterResolverInterface
     */
    protected function getParameterResolver()
    {
        return $this->parameterResolver;
    }
}
