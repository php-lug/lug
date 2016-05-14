<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Bundle\ResourceBundle\Security;

use Lug\Bundle\ResourceBundle\Routing\ParameterResolverInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class SecurityChecker implements SecurityCheckerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var ParameterResolverInterface
     */
    private $parameterResolver;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ParameterResolverInterface    $parameterResolver
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ParameterResolverInterface $parameterResolver
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($action, $object)
    {
        if (!$this->parameterResolver->resolveVoter()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('lug.'.$action, $object);
    }
}
