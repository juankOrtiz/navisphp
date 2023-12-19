# Logging

<< Anterior: [Mantenimiento](mantenimiento.md)

\>> Siguiente: [Jobs](jobs.md)

-   [Introduccion](#introduccion)
-   [Clase Logger](#clase-logger)
-   [Tipos de niveles](#tipos-de-niveles)
-   [Ejemplo de uso](#ejemplo-de-uso)
-   [Referencia de Monolog](#referencia-de-monolog)

## Introduccion

Logging es la capacidad de enviar mensajes a archivos de extensión .log que sirven como registro de las operaciones de un sistema. En un servidor por lo general se envían los logs de errores. NavisPHP utiliza la librería [Monolog](https://github.com/Seldaek/monolog) para crear logs.

## Clase Logger

La clase `Core\Logger` es la clase encargada de crear nuevos logs, implementando la clase del mismo nombre de Monolog. Consta de tres métodos:

-   `__construct($channel)` que se encarga de crear la instancia de la clase y establecer la configuración por defecto, de modo que el desarrollador no tenga que realizar muchas operaciones antes de utilizarla. Se puede cambiar el nombre del canal que se usará para los logs en lugar del valor por defecto _app_.

-   `setLogfile($filename)` que se encarga de cambiar el nombre del archivo de logs utilizado (por defecto es `jobs/changes.log`).

-   `save($message, $context, $level)` que se encarga de guardar el mensaje del log en el archivo. Además del mensaje, se puede establecer el contexto en forma de arreglo, indicando las variables contextuales que se desean asignar, y el nivel de logging (por defecto es debug).

### Tipos de niveles

-   **DEBUG** (100): información de debug.

-   **INFO** (200): eventos interesantes, como inicios de sesión o consultas SQL.

-   **NOTICE** (250): eventos normales pero significantes.

-   **WARNING** (300): ocurrencia de excepciones que no son errores, como el uso de librerías obsoletas, pero que aun funcionan.

-   **ERROR** (400): eventos en tiempo de ejecución que no requieren acción inmediata pero que deberían ser loggeados y monitoreados.

-   **CRITICAL** (500): condiciones críticas, como ser excepciones inesperadas.

-   **ALERT** (550): acciones que deben ser tomadas de forma inmediata. Ejemplos: todo el sitio está caído, la base de datos no está disponible.

-   **EMERGENCY** (600): el sistema no se puede utilizar.

## Ejemplo de uso

Para crear un log basta importar la clase, crear una instancia de la misma y luego llamar al método save().

```php
use Core\Logger;

$log = new Logger('jobs');

$log->save(
    message: 'Log del job test.php',
    context: ['fecha' => 'Log creado a las ' . $date],
    level: 'info');
```

## Referencia de Monolog

En el siguiente enlace puedes acceder a la [documentación de Monolog](https://github.com/Seldaek/monolog/blob/main/doc/01-usage.md) para profundizar en las características de dicha librería.

[Volver al inicio](#logging)

<< Anterior: [Mantenimiento](mantenimiento.md)

\>> Siguiente: [Jobs](jobs.md)
