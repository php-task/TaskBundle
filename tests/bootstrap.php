<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$file = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($file)) {
    throw new RuntimeException('Install dependencies to run test suite.');
}

$loader = require $file;

require __DIR__ . '/app/TestKernel.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
