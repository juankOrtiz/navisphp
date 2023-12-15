# Configuracion

-   [Introduccion](#introduccion)
-   [Archivo config](#archivo-config)
-   [Clase Settings](#clase-settings)

## Introduccion

NavisPHP toma prestada la ideología de otros frameworks de que el comportamiento de una aplicación debería poder configurarse con facilidad. Entre los aspectos configurables encontramos funcionalidades tales como:

-   Características de la aplicación (URL, zona horaria para la generación de fechas, etc)
-   La conexión a una base de datos
-   La conexión a un servicio de envío de mails
-   El enlace con API's o servicios externos
-   Características del modo mantenimiento

Esto se realiza mediante dos componentes provistos por el framework: el archivo `config.php` y la clase `Core\Settings.php`.

## Archivo config

El archivo `config.php` contiene todas las credenciales y variables de configuración de la aplicación. Por defecto no existe en el proyecto, sino que debe ser creado como una copia del archivo `config.example.php`.

> [!INFO]
> Se recomienda que cada vez que surja la necesidad de agregar un grupo de credenciales, se modifique el archivo `config.example.php` para tener una versión sincronizada del esquema de configuración.

> [!WARNING]
> `config.php` nunca debería ser agregado al control de versiones de Git, ya que posee información sensible como claves y contraseñas. Si bien se encuentra por defecto en el gitignore, el programador nunca debería cambiar este comportamiento.

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

## Clase Settings

La clase `Core\Settings` es la encargada de leer todos los valores definidos en el archivo de configuración, proveyendo acceso a las variables de dicho archivo al resto de la aplicación.

Al instanciar la clase debemos indicar cual es el grupo de credenciales al cual deseamos acceder. Por ejemplo: si creamos un objeto con el argumento _"database"_, el constructor leerá el archivo `config.php` y devolverá el arreglo de todas las credenciales bajo ese nombre.

El método _get($key)_ es el encargado de obtener la credencial específica del grupo al cual estamos accediendo.

```php
// Constructor de Settings.php
public function __construct($config) {
    $archivo = require base_path('config.php');
    $this->config = $archivo[$config];
}

// En otro archivo agregamos lo siguiente:
use Core\Settings;

$database = new Settings('database');
$username = $database->get('username')
```
