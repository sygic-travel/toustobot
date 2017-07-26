<?php declare(strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Toustobot\LunchMenu\GetMenuCommand;
use Symfony\Component\Console\Application;
use Tracy\Debugger;


Debugger::enable(false);

$console = new Application();
$console->setCatchExceptions(false);
$console->add(new GetMenuCommand());
$console->run();
