<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Lug\Component\Grid\Column\Type\Formatter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface FormatterInterface
{
    /**
     * @param mixed   $value
     * @param mixed[] $options
     *
     * @return mixed
     */
    public function format($value, array $options = []);
}
