# Mantenimiento

-   [Introduccion](#introduccion)
-   [Archivo config](#archivo-config)
-   [Modo mantenimiento](#modo-mantenimiento)
-   [Mecanismo de mantenimiento](#mecanismo-de-mantenimiento)

## Introduccion

A la hora de introducir nuevos cambios en un sistema que está en producción, es necesario impedir que los usuarios del mismo puedan acceder al mismo, dado que los cambios introducidos podrían hacer que la experiencia de uso sea inestable.

Por ello se introduce un mecanismo de mantenimiento que es fácilmente manejable desde la configuración.

## Archivo config

Dentro de las propiedades del archivo `config.php` existe el arreglo de las variables de mantenimiento:

```php
'maintenance' => [
    'status' => 0,
    'allowed_routes' => [
        '/login',
        '/session'
    ],
],
```

La clave `status` indica si el modo mantenimiento se encuentra encendido o no.

La clave `allowed_status` indica cuales son las rutas a las cuales aun se pueden acceder cuando el modo mantenimiento está encendido.

## Modo mantenimiento

Para iniciar el modo mantenimiento basta con cambiar el valor de la clave `status` a **1**. Una vez iniciado este modo, se cumplirán las siguientes reglas:

-   Solamente los usuarios de tipo **Super Admin** podrán acceder a la totalidad del sistema. En NavisPHP este tipo de usuarios tienen por defecto un tipo asignado al valor 1 (ver archivo `Core\functions.php`)

-   Los demás usuarios del sistema podrán acceder solamente a las rutas definidas dentro de la clave `allowed_routes`. Solamente se podrán agregar a este arreglo las rutas que se corresponden con el método **GET**.

-   Si un usuario intenta acceder a una ruta no permitida en este modo, se devolverá un error **503** con la correspondiente vista `views\503.php`

Para finalizar el modo mantenimiento basta con cambiar el valor de la clave `status` a **0**; de esta forma el sitio volverá a su funcionamiento normal.

## Mecanismo de mantenimiento

El único lugar de la aplicación donde se aplica el mecanismo de mantenimiento es en `public\index.php` antes de finalizar cada request de la aplicación.

```php
$maintenance = new Settings('maintenance');

if ($maintenance->get('status') === 1) {
    $rutas_permitidas = $maintenance->get('allowed_routes');
    if (!in_array($uri, $rutas_permitidas, true)) {
        if (user() && !isSuperAdmin()) {
            http_response_code(Response::MAINTENANCE);
            require base_path("views/503.php");
            die();
        }
    }
}
```
