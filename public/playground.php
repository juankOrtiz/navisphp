<?php

use Illuminate\Support\Collection;

require __DIR__.'/../vendor/autoload.php';

$numeros = new Collection([
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10
]);

if($numeros->contains(10)) {
    die('Contiene el numero 10');
}