<?php

$loader = require(__DIR__.'/../../vendor/autoload.php');
$loader->add('Controller', __DIR__.'/../src');
$loader->add('Knp\\FcTestBundle', __DIR__.'/../src');

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
