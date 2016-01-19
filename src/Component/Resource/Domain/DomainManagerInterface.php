<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Resource\Domain;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface DomainManagerInterface
{
    /**
     * @param string  $action
     * @param string  $repositoryMethod
     * @param mixed[] $criteria
     * @param mixed[] $sorting
     *
     * @return mixed
     */
    public function find($action, $repositoryMethod, array $criteria, array $sorting);

    /**
     * @param object $object
     * @param bool   $flush
     */
    public function create($object, $flush = true);

    /**
     * @param object $object
     * @param bool   $flush
     */
    public function update($object, $flush = true);

    /**
     * @param object $object
     * @param bool   $flush
     */
    public function delete($object, $flush = true);

    /**
     * @param object|null $object
     */
    public function flush($object = null);
}
