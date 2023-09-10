<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

/*
 * Let's clean up the sandbox.
 */
$di = new DirectoryIterator(__DIR__ . '/sandbox');
$di = new RegexIterator($di, '/.php$/');

/** @var DirectoryIterator $file */
foreach ($di as $file) {
    unlink($file->getPathname());
}
