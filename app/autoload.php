<?php

/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

// FIXME - https://github.com/puli/issues/issues/190
if (file_exists($puliFactory = __DIR__.'/../.puli/GeneratedPuliFactory.php')) {
    $loader->addClassMap(['Puli\\GeneratedPuliFactory' => $puliFactory]);
}

return $loader;
