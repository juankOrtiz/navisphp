# Jobs

-   [Introduccion](#introduccion)
-   [Estructura de un job](#estructura-de-un-job)
-   [Tipos de niveles](#tipos-de-niveles)
-   [Ejemplo de uso](#ejemplo-de-uso)
-   [Referencia de Monolog](#referencia-de-monolog)

## Introduccion

En un sitio en producción muchas veces nos encontraremos con la necesidad de ejecutar ciertas tareas de forma repetitiva bajo ciertos parámetros, donde ejecutarlas de forma manual no es una opción.

Los _jobs_ son tareas programadas que se ejecutan en un servidor. En NavisPHP no se implementa un mecanismo de programación de tareas, sino que todo archivo que se encuentre en la carpeta `jobs` define la estructura de código de una tarea, y en un servidor se puede utilizar el comando [Cron](https://en.wikipedia.org/wiki/Cron) para programarla y definir los parámetros de ejecución.

## Estructura de un job

Debido a que el comando Cron se encarga de programar solamente un archivo, los jobs no accederán al ciclo de vida de la aplicación ni a los contenedores. Así que para utilizar las clases de la aplicación se deberán cargar manualmente los procesos principales.

El siguiente código está basado en el archivo `jobs\test.php`; se comienza cargando los componentes principales de la aplicación de forma manual:

```php
<?php

const BASE_PATH = __DIR__ . '/../';

require BASE_PATH . 'vendor/autoload.php';

require BASE_PATH . 'Core/functions.php';

require BASE_PATH . 'bootstrap.php';
```

Luego se aplica la lógica propia de cada tarea. En este caso, se calcula la fecha actual y luego mediante la clase `Core\Logger` se crea un log de sistema con información de test:

```php
use Core\Logger;

date_default_timezone_set('America/Argentina/Buenos_Aires');
$date = date('m/d/Y h:i:s a', time());

$log = new Logger('jobs');

$log->save(message: 'Log del job test.php', context: ['fecha' => 'Log creado a las ' . $date], level: 'info');
```

Nota que se define un nuevo canal con el nombre _jobs_ al crear el log. Los jobs son un caso típico de procesos que deberían loggearse para comprender si la tarea programada se ha llevado a cabo o no.

Para más información sobre el funcionamiento de Logger puedes visitar la documentación de [Logging](logging.md)
