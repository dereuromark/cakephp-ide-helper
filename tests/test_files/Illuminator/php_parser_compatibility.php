<?php
declare(strict_types=1);

require dirname(__DIR__, 3) . '/vendor/autoload.php';

class_exists($argv[1]);
(new \PhpParser\ParserFactory())->createForNewestSupportedVersion();
