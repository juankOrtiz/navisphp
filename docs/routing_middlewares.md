# Routing y middlewares

<< Anterior: [Configuración](configuracion.md)

\>> Siguiente: [Controladores](controladores.md)

-   [Introduccion](#introduccion)
-   [Lectura de URI](#lectura-de-uri)
-   [Archivo routes](#archivo-routes)
-   [Clase Router](#clase-router)
-   [Middlewares](#middlewares)
-   [Clase Middleware](#clase-middleware)
-   [Crear un middleware](#crear-un-middleware)

## Introduccion

Al analizar el [ciclo de vida](ciclo_vida.md) vimos que uno de los pasos más importantes es el análisis de la URI seleccionada por la aplicación o el usuario y la respuesta de la aplicación en consecuencia. Veamos como funciona el routing o "enrutamiento" en NavisPHP.

## Lectura de URI

Los primeros pasos necesarios es analizar cual es la URI y el método asociado que se desean visitar. Retomamos `public/index.php`:

```php
// Se crea la instancia del Router
$router = new \Core\Router;
// Se cargan las rutas definidas para la aplicación
$routes = require base_path('routes.php');

// Si la dirección requerida es "https://sitio.com/dashboard"
// la variable $uri tendrá el valor "/dashboard"
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

// Si el enlace proviene de un formulario, se analiza si es un método personalizado
// Ejemplo: PUT, PATCH, DELETE
// Sino, leemos el método de $_SERVER: GET o POST
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
```

Con estos dos datos, el siguiente paso es intentar "mapear" la URI con una acción definida, lo cual es responsabilidad de un controlador. Pero para llegar a ese punto analicemos primero dos archivos importantes.

## Archivo routes

En la raíz del proyecto nos encontramos con `routes.php`, archivo que define la lista de URL's permitidas para nuestra aplicación. Para cada URL también se define cual es el [controlador](controladores.md) que se ejecutará al solicitar dicha dirección.

```php
$router->get('/', 'index.php');

$router->get('/login', 'session/create.php')->only('guest');
$router->post('/session', 'session/store.php')->only('guest');
$router->delete('/session', 'session/destroy.php')->only('auth');
```

En el ejemplo vemos definidas 4 rutas, cada una de ellas con su respectiva función que equivale a un método HTTP, y como parámetros definen la URL de la ruta y por último el controlador que se ejecutará como respuesta al llamado a la URL.

El método _only()_ y sus parámetros definen si para cada ruta se define un [middleware](#middlewares) que se ejecutará antes de llegar al controlador.

## Clase Router

La clase `Core\Router` es la encargada de trabajar con las rutas de la aplicación. Si bien contiene varios métodos, mencionamos los 3 más importantes:

-   `add()`: permite agregar una ruta al arreglo de rutas de la aplicación, definiendo la URI, el controlador, el método HTTP y el middleware que afecta a la ruta.
-   `__cal()`: es una función mágica que toma cada una de las funciones con los nombres de métodos que vemos en `routes.php` (por ejemplo, _get()_, _post()_, etc) y llama al método _add()_ para agregar las rutas correspondientes a ser analizadas.
-   `route()`: es el método que resuelve si la ruta recibida como parámetro está registrada en la aplicación y tiene un controlador que responsa en consecuencia.

Recordamos que en `index.php` ya se había instanciado esta clase para crear un enrutador. Ahora resta resolver el mapeo en el siguiente bloque: si la ruta existe, se ejecuta el controlador designado.

```php
try {
    // Se llama al método route() para intentar cargar el controlador de respuesta
    $router->route($uri, $method);
}
```

## Middlewares

La última pieza de la ecuación son los middlewares. Estos no son más que clases cuyo único propósito es definir reglas que cada ruta debe cumplir antes de proseguir con el controlador objetivo. Si la aplicación no cumple con las reglas impuestas por el middleware asociado a dicha ruta, esta clase definirá que ocurre con la aplicación.

Retomando la clase `Core\Router`, vemos que el método _only()_ es el que aplica un middleware a una ruta puntual:

```php
public function only(string $key): self
{
    // Agregar al arreglo de middlewares de la ruta el nombre del middleware $key
    $this->routes[array_key_last($this->routes)]['middleware'] = $key;

    return $this;
}
```

Una vez que se trata de resolver la ruta en el método _route()_ se procede a ejecutar el middleware:

```php
public function route(string $uri, string $method)
{
    foreach ($this->routes as $route) {
        if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
            // Se resuelve el middleware afectado a esta ruta
            // Cada middleware define si la aplicación pasa este control
            Middleware::resolve($route['middleware']);

            return require base_path('Http/controllers/' . $route['controller']);
        }
    }

    $this->abort();
}
```

## Clase Middleware

Todos los middlewares están almacenados en `Core\Middleware`, incluída la clase base del mismo nombre.

```php
namespace Core\Middleware;

class Middleware
{
    // Se mapean todos los middlewares definidos en la aplicación
    public const MAP = [
        'guest' => Guest::class,
        'auth' => Auth::class,
    ];

    // Resolver un middleware puntual
    public static function resolve($key)
    {
        // Si no existe un middleware con dicho nombre, salimos de la función
        if(!$key) {
            return;
        }

        // Tomamos el nombre de la clase que corresponde a la clave
        $middleware = static::MAP[$key] ?? false;

        // Si no se encuentra la clase mapeada, se arroja una excepción
        if(!$middleware) {
            throw new \Exception("There is no middleware with the name '{$key}'.");
        }

        // Si se pasaron las comprobaciones, es momento de ejecutar el middleware
        // mediante su método interno handle()
        (new $middleware)->handle();
    }
}
```

Veamos a modo de ejemplo el middleware `Auth.php`, el cual se encarga de controlar si un usuario ha iniciado sesión en la aplicación.

```php
namespace Core\Middleware;

class Auth
{
    public function handle()
    {
        // Si no existe la clave 'user' en $_SESSION
        // es porque no hay una sesión iniciada
        if(!$_SESSION['user'] ?? false) {
            // En dicho caso, redireccionamos a la ruta base
            header('location: /');
            exit();
        }
    }
}
```

## Crear un middleware

Para crear un nuevo middleware en nuestra aplicación debemos seguir los siguientes pasos:

1. Crear una nueva clase en la carpeta `Core/Middleware` con el nombre del middleware.

2. Agregar un método llamado _handle()_ y agregar la comprobación que debe pasar la aplicación y que acciones tomar en caso de que no se cumpla.

3. Modificar la clase `Core\Middleware\Middleware.php` y agregar el nombre de la nueva clase a la constante MAP.

[Volver al inicio](#routing-y-middlewares)

<< Anterior: [Configuración](configuracion.md)

\>> Siguiente: [Controladores](controladores.md)
