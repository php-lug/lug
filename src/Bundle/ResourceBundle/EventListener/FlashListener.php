<?php
/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\EventListener;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Lug\Component\Resource\Domain\DomainEvent;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FlashListener
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param Session                    $session
     * @param ParameterResolverInterface $parameterResolver
     */
    public function __construct(Session $session, ParameterResolverInterface $parameterResolver)
    {
        $this->session = $session;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * @param DomainEvent $event
     */
    public function addFlash(DomainEvent $event)
    {
        if (!$this->parameterResolver->resolveApi()) {
            $this->session->getFlashBag()->add($event->getMessageType(), $event->getMessage());
        }
    }
}
