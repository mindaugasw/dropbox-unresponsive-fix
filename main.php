<?php

declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/config.local.php';

foreach (glob(__DIR__ . '/src/{*,*/*}.php', GLOB_BRACE) as $file) {
    require $file;
}

$processor = Processor::getInstance();
$processor->start();
