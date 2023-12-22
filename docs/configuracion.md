# Configuracion

<< Anterior: [El ciclo de vida de la aplicación](ciclo_vida.md)

\>> Siguiente: [Routing y middlewares](routing_middlewares.md)

-   [Introduccion](#introduccion)
-   [Archivo config](#archivo-config)
-   [Método config](#método-config)

## Introduccion

NavisPHP toma prestada la ideología de otros frameworks de que el comportamiento de una aplicación debería poder configurarse con facilidad. Entre los aspectos configurables encontramos funcionalidades tales como:

-   Características de la aplicación (URL, zona horaria para la generación de fechas, etc)
-   La conexión a una base de datos
-   La conexión a un servicio de envío de mails
-   El enlace con API's o servicios externos
-   Características del modo mantenimiento

Esto se realiza mediante dos componentes provistos por el framework: el archivo `config.php` y el método `config`.

## Archivo config

El archivo `config.php` contiene todas las credenciales y variables de configuración de la aplicación. Por defecto no existe en el proyecto, sino que debe ser creado como una copia del archivo `config.example.php`.

> [!INFO]
> Se recomienda que cada vez que surja la necesidad de agregar un grupo de credenciales, se modifique el archivo `config.example.php` para tener una versión sincronizada del esquema de configuración.

> [!WARNING] > `config.php` nunca debería ser agregado al control de versiones de Git, ya que posee información sensible como claves y contraseñas. Si bien se encuentra por defecto en el gitignore, el programador nunca debería cambiar este comportamiento.

El archivo `config.php` simplemente devuelve un arreglo con los grupos de credenciales necesarios. Veamos una parte resumida del código:

```php
return [
    'database' => [
        'username' => '',
        'password' => '',
        'host' => '',
        'port' => 3306,
        'dbname' => '',
        'charset' => 'utf8mb4'
    ],
```

Estos valores vacíos deben ser completados con las credenciales de nuestra conexión a la base de datos de la aplicación.

Pero para que la aplicación pueda acceder a estos datos es necesaria la ayuda de otra clase.

## Método config

El método `functions\config` es el encargado de leer los valores definidos en el archivo de configuración, brindando acceso a las variables de dicho archivo al resto de la aplicación. Recibe como argumento una cadena que puede poseer la clave de un grupo de variables al cual deseamos acceder, en cuyo caso nos devolverá el arreglo completo de dicho grupo.

```php
$app = config('app'); // Devuelve el arreglo app

// Para obtener un valor dentro de este arreglo podemos usar los corchetes
$url = $app['url'];
$timezone = $app['timezone'];
```

Si deseamos obtener un valor único también podemos usar la _"dot notation"_, usando un punto para definir el arreglo general y luego la clave específica que deseamos guardar.

```php
$url = config('app.timezone'); // Devuelve el valor de timezone.
```

[Volver al inicio](#configuracion)

<< Anterior: [El ciclo de vida de la aplicación](ciclo_vida.md)

\>> Siguiente: [Routing y middlewares](routing_middlewares.md)
