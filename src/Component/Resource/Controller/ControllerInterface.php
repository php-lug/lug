<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ControllerInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request);

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function gridAction(Request $request);

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function batchAction(Request $request);

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request);

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request);

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request);

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request);
}
