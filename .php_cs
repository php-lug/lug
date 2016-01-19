<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\FixerInterface;

$finder = DefaultFinder::create()
    ->in(['src', 'web'])
    ->exclude('bower_components')
    ->files([
        'app/AppKernel.php',
        'app/AppCache.php',
        'app/autoload.php',
        'app/console',
    ]);

return Config::create()
    ->level(FixerInterface::SYMFONY_LEVEL)
    ->fixers(['align_double_arrow', 'ordered_use', 'phpdoc_order', 'short_array_syntax'])
    ->setUsingCache(true)
    ->finder($finder);
