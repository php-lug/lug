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

$finder = DefaultFinder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/src',
        __DIR__.'/web',
    ])
    ->exclude([
        'cache',
        'logs',
    ])
    ->notName('check.php')
    ->notName('SymfonyRequirements.php');

return Config::create()
    ->setUsingCache(true)
    ->fixers([
        'align_double_arrow',
        'short_array_syntax',
        'ordered_use',
    ])
    ->finder($finder);
