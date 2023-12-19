# Bases de datos

<< Anterior: [Controladores](controladores.md)

\>> Siguiente: [Validaciones](validaciones.md)

-   [Introduccion](#introduccion)
-   [Credenciales](#credenciales)
-   [Clase Database](#clase-database)
-   [Uso de Database](#uso-de-database)
-   [El método query](#el-método-query)
-   [Lectura de datos](#lectura-de-datos)
-   [Inserción de datos](#inserción-de-datos)
-   [Actualización de datos](#actualización-de-datos)
-   [Eliminación de datos](#eliminación-de-datos)
-   [La carpeta database](#la-carpeta-database)

## Introduccion

El acceso a las bases de datos es una característica fundamental de muchas aplicaciones web. NavisPHP no hace uso de ninguna librería u ORM especializado para esta tarea, sino que define su propia abstracción por encima de PDO, lo que permite conectarse a distintos motores de forma nativa sin la necesidad de instalar paquetes o librerías externas.

## Credenciales

Para conectarse a la base de datos de la aplicación primero se deben definir las correspondientes credenciales en el archivo `config.php`. A modo de ejemplo:

```php
'database' => [
    'username' => 'root',
    'password' => '',
    'host' => 'localhost',
    'port' => 3306,
    'dbname' => 'navisphp',
    'charset' => 'utf8mb4',
],
```

> [!WARNING]
> Las credenciales de conexión nunca deberían almacenarse en ninguna parte de la aplicación que sea accesible desde el navegador web ni tampoco debe ser subida al sistema de control de versiones.

## Clase Database

La abstracción de PDO se realiza mediante la clase `Core\Database`, la cual soporte actualmente solamente los motores MySQL y PostgreSQL. El constructor de esta clase se encarga de definir cual es el motor de la conexión a abrir, dado que el DSN difiere para ambos casos.

```php
public function __construct($config, string $db = 'mysql')
{
    $dsn = $db . ':' . http_build_query($config, '', ';');
    $db_user = $db === 'mysql' ? $config['username'] : $config['user'];

    $this->connection = new PDO($dsn, $db_user, $config['password'], [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}
```

Una vez realizada la comprobación, se crea una nueva instancia de PDO con las credenciales obtenidas.

## Uso de Database

Como se explica en la documentación del [ciclo de vida](ciclo_vida.md), se puede crear una nueva instancia de Database mediante el contenedor de la aplicación.

```php
use Core\App;
use Core\Database;

// En $db tendremos el objeto de PDO que nos permitirá interactuar con la base de datos
$db = App::resolve(Database::class);
```

## El método query

Database provee el método _query()_ para realizar consultas a la base de datos. El método requiere dos argumentos:

1. La consulta a ejecutar, que es una cadena en formato SQL. Dentro de esta cadena podemos agregar "comodines" que, al momento de ejecutar la consulta, serán remplazados con los valores de las variables del segundo argumento.
2. _(Opcional)_ Las variables que queremos agregar a la consulta. En lugar de pasar las variables como parte de la consulta, lo hacemos con este arreglo para evitar problemas de seguridad como las inyecciones SQL.

Llamar a este método hará que la consulta se ejecute inmediatamente, pero también podemos encadenar otros si deseamos modificar la naturaleza del resultado.

## Lectura de datos

Si deseamos ejecutar una consulta `SELECT` que devuelva más de un registro podemos encadenar el método _get()_, el cual devuelve un arreglo multidimensional.

```php
$users = $db->query(
    "SELECT *
    FROM users"
)->get();
```

En cambio, si el objetivo de la consulta es obtener un solo registro, el método a usar es _find()_, el cual devuelve un arreglo unidimensional.

```php
$userId = 1;

$user = $db->query(
    "SELECT *
    FROM users
    WHERE idusers = :id", [
        "id" => $iduser,
    ]
)->find();
```

Como alternativa, podemos indicar que si no se encuentra el registro indicado se devuelva un error 404 con el método _findOrFail()_

```php
$userId = 1;

// Si no existe el usuario de ID 1, se devuelve un error 404
$user = $db->query(
    "SELECT *
    FROM users
    WHERE idusers = :id", [
        "id" => $iduser,
    ]
)->findOrFail();
```

## Inserción de datos

Para ejecutar una consulta `INSERT` basta con ejecutar el método _query()_ sin ningún método adicional.

```php
$db->query(
    "INSERT INTO users(name, email)
    VALUES(:name, :email)", [
        'name' => $name,
        'email' => $email
    ]);
```

Adicionalmente, podemos obtener el número de filas afectadas mediante _rowCount()_

```php
// $rows debería ser igual a 1
$rows = $db->query(
    "INSERT INTO users(name, email)
    VALUES(:name, :email)", [
        'name' => $name,
        'email' => $email
    ]
)->rowCount();
```

## Actualización de datos

Para ejecutar una consulta `UPDATE` seguimos las mismas reglas vistas para los `INSERT`.

```php
$db->query(
    "UPDATE users
    SET name = :name
    WHERE idusers = :id",
    [
        'name' => $name,
        'id' => $iduser,
    ]
);
```

## Eliminación de datos

Para ejecutar una consulta `DELETE` seguimos las mismas reglas vistas para los `INSERT`.

```php
$db->query(
    "DELETE FROM users
    WHERE idusers = :id",
    [
        'id' => $iduser,
    ]
);
```

## La carpeta database

Si bien no es una parte fundamental de la clase de consultas, la carpeta `database` viene por defecto con un archivo `script.sql` que contiene la estructura base de una base de datos usada por el framework: la creación de la base de datos y de una sencilla tabla de usuarios que puede utilizarse como punto de partida.

A partir de allí, puedes optar por ampliar este script con las demás tablas de tu base de datos o guardar otros tipos de scripts que sean útiles.

[Volver al inicio](#bases-de-datos)

<<< Anterior: [Controladores](controladores.md)

\>> Siguiente: [Validaciones](validaciones.md)
