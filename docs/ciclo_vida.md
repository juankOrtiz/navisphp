# Ciclo de vida de la aplicación

-   [Introduccion](#introduccion)
-   [El ciclo de vida](#el-ciclo-de-vida)
-   [Clase App](#clase-app)
-   [Clase Container](#clase-container)
-   [Preguntas frecuentes](#preguntas-frecuentes)

## Introduccion

Entender el ciclo de vida de la aplicación es vital para comprender como funciona NavisPHP cada vez que un usuario realiza una acción.

## El ciclo de vida

Cuando se realiza una acción en cualquier aplicación hecha con NavisPHP, esta acción transcurre por diversas etapas hasta llegar a su destino final. Por lo general, las acciones implican visitar una URL, lo cual desencadena una serie de acciones que ocurren en un orden preciso.

El ciclo de vida resumido es el siguiente:

1. El usuario visita una URL, ya sea de forma manual o mediante un enlace.

2. Como el servidor se ejecuta solamente sobre la carpeta `/public`, el servidor ejecuta el archivo `index.php` que se encuentra dentro.

3. Lo primero que se realiza es cargar los componentes necesarios:

```php
// Se define la ruta base para cargar el resto de los archivos
const BASE_PATH = __DIR__ . '/../';

// Se carga el autoloader
require BASE_PATH . 'vendor/autoload.php';

// Inicia la sesión: esto crea la variable superglobal $_SESSION
session_start();

// Se cargan las funciones comunes a toda la aplicación
require BASE_PATH . 'Core/functions.php';
```

El autoloader es el componente encargado de manejar los namespaces y cargar las clases en cada archivo cada vez que es requerido. Se utiliza el autoloader de **Composer** para esta tarea.

4. La siguiente línea carga el archivo `bootstrap.php`, el cual se encarga de crear el contenedor de la aplicación.

```php
// public/index.php
require base_path('bootstrap.php');

// boostrap.php
use Core\App;
use Core\Container;
use Core\Database;

// Se crea un contenedor
$container = new Container;

// Se agregan al contenedor las clases necesarias
// Esto se conoce como inyección de dependencias
$container->bind('Core\Database', function () {
    $config = require base_path('config.php');

    return new Database($config['database']);
});

// Se configura el contenedor como el contenedor por defecto de App
App::setContainer($container);
```

Aquí tenemos dos clases adicionales: `Core/App` y `Container/Container`, los cuales son explicados en sus propias secciones.

5. Las siguientes líneas de `public/index.php` crean la instancia del [router](routing_middlewares.md), encargado de mapear cada URL con una acción específica de respuesta.

```php
// Se crea el router
$router = new \Core\Router();
// Se carga el listado de todas las rutas registradas
$routes = require base_path('routes.php');
// Se obtiene la URI del pedido HTTP actual
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
// Se define cual es el método HTTP que acompaña al pedido HTTP
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
```

6. El siguiente paso es confirmar si el sitio se encuentra en [mantenimiento](mantenimiento.md), en cuyo caso se analizarán las limitaciones pertinentes.

```php
$maintenance = new Settings('maintenance');
// Si el sitio está en mantenimiento, se dejará pasar solo a los super admins
// o a los usuarios que visiten las rutas permitidas
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

7. Luego se intenta dirigir al usuario a la próxima ruta, a menos que exista un error de validación en cuyo caso se redirige a la ruta anterior con los datos ingresados previamente (útil para volver a poblar un formulario) y los mesajes de error.

```php
try {
    // Se intenta mapear la ruta ingresada con su correspondiente controlador
    $router->route($uri, $method);
} catch (ValidationException $exception) {
    // Se definen los mensajes de error y datos previos en la sesión
    Session::flash('errors', $exception->errors);
    Session::flash('old', $exception->old);

    return redirect($router->urlAnterior());
}
```

Si no surgió ningún error, a partir de ahora es el [controlador](controladores.md) de la respectiva ruta el que toma el control y realiza sus propias acciones.

8. Por último, se eliminan los datos que han sido agregados a la sesión mediante el método _flash()_. De esta forma, esos datos existirán solamente para el pedido actual.

```php
Session::unflash();
```

## Clase App

La clase `Core\App` es una clase sencilla que representa nuestra aplicación completa. La función principal es asignar un contenedor que contenga las dependencias necesarias para funcionar.

```php
namespace Core;

class App
{
    protected static Container $container;

    // Asignar un contenedor a la aplicación
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    // Obtener el contenedor (getter)
    public static function container(): Container
    {
        return static::$container;
    }

    // Delegar la llamada del método bind() a la clase Container
    public static function bind($key, $resolver): void
    {
        static::container()->bind($key, $resolver);
    }

    // Delegar la llamada del método resolve() a la clase Container
    // Esto evita que tengamos que encadenar varios métodos, como:
    // App::container()->resolve(Database::class);
    // En lugar de eso, haríamos lo siguiente:
    // App::resolve(Database::class);
    public static function resolve($key)
    {
        return static::container()->resolve($key);
    }
}
```

Como su objetivo principal es obtener las clases asignadas al contenedor de la aplicación, su uso es bastante simple: cada vez que necesitemos obtener una clase del contenedor, simplemente llamamos al método _resolve()_

```php
// De esta forma obtenemos la clase Database para hacer consultas
App::resolve(Database::class)
```

Otro detalle de esta clase es que todos sus métodos son estáticos, es decir que no hace falta crear una instancia de App para interactuar con ella.

## Clase Container

La clase `Core\Container` define la estructura de un contenedor. Si bien este concepto puede extenderse hasta ser muy complejo, la implementación actual es bastante concreta:

-   Cuando queremos agregar una clase al contendor usamos el método _bind()_
-   Más adelante, cuando querramos obtener la clase que ha sido agregada al contendor usamos el método _resolve()_

```php
namespace Core;

class Container
{
    protected array $bindings = [];

    // Agregar elementos al contenedor.
    // $key es el nombre que tendrá la clase
    // $resolver es la función que se encarga de mapear la clase al contenedor
    public function bind($key, $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    // Obtener elementos del contenedor
    public function resolve($key)
    {
        if(!array_key_exists($key, $this->bindings)) {
            throw new \Exception("No matching binding found for {$key}");
        }

        $resolver = $this->bindings[$key];

        return call_user_func($resolver);
    }
}
```

## Preguntas frecuentes

## ¿Que clases podemos agregar al contenedor?

Si bien es posible agregar múltiples clases al contenedor de la aplicación, saturar el contenedor no suele ser una buena idea. Los siguientes puntos pueden ayudarnos a decidir en qué casos conviene cargar una clase al contenedor:

1. Si es una clase propensa a ser usada en varias partes de nuestra aplicación.

2. Si la instanciación de la clase requiere la configuración de varios argumentos o credenciales, haciendo que ese proceso ensucie el código cada vez que se crea un objeto.

3. Si la implementación de la clase no requiere de otra clase o de la ejecución de un método previo de configuración que compliquen la resolución del contenedor.

### ¿Dónde debemos agregar clases al contenedor?

El archivo `bootstrap.php` es el encargado de crear el contenedor y agregar las clases necesarias. Si deseamos agregar una nueva clase, basta con utilizar una llamada al método _bind()_ sobre el contenedor.

### ¿En qué momento se crea la instancia de cada clase?

Debemos notar que al agregar una clase al contenedor aun no se crea la instancia de dicha clase. Si volvemos a revisar el contenido de `bootstrap.php` veremos que el método _bind()_ solamente se encarga de "mapear" el nombre de una clase (en el ejemplo es `Core\Database`) con la función callback que será llamada cuando se necesite obtener dicha clase del contenedor.

```php
$container->bind('Core\Database', function () {
    $config = require base_path('config.php');

    return new Database($config['database']);
});
```

Veamos ahora el archivo `Core\Authenticator` que utiliza dicha clase del contenedor para hacer consultas a la base de datos.

```php
public function atempt(string $email, string $password): bool
{
    $user = App::resolve(Database::class)
        ->query('select * from users where email = :email', [
            'email' => $email
        ])->find();
    // resto del método
}
```

Al utilizar el método _resolve()_ se busca en el contenedor la clase con la clave `Database` y se ejecuta la función callback que había sido definida como segundo argumento del método _bind()_, la cual crea en ese momento la instancia de la clase.
