<?php

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';

require BASE_PATH . 'Core/functions.php';

require BASE_PATH . 'bootstrap.php';

use Core\Logger;

date_default_timezone_set('America/Argentina/Buenos_Aires');
$date = date('m/d/Y h:i:s a', time());

$log = new Logger('jobs');

$log->save(message: 'Log del job test.php', context: ['fecha' => 'Log creado a las ' . $date], level: 'info');
