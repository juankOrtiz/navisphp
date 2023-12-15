# Sesiones y Autenticación

-   [Introduccion](#introduccion)
-   [Rutas de autenticación](#rutas-de-autenticación)
-   [Clase Session](#clase-session)
-   [Agregar datos a la sesión](#agregar-datos-a-la-sesión)
-   [Agregar datos temporales](#agregar-datos-temporales)
-   [Eliminar datos de sesión](#eliminar-datos-de-sesión)
-   [Métodos de utilidad](#métodos-de-utilidad)

## Introduccion

La autenticación es el mecanismo que permite al sistema controlar que un usuario tenga una sesión activa, iniciar una nueva sesión con las credenciales de un usuario particular o eliminarla. De esta forma la aplicación puede realizar sus tareas teniendo a disposición la información de la sesisón del usuario activa y usarla para distintos propósitos.

Estos procesos se valen a su vez del uso del sistema de sesiones de PHP que permite conservar los valores asignados a la superglobal $\_SESSION y compartirlos con varias partes de la aplicación.

## Rutas de autenticación

NavisPHP provee las siguientes rutas y controladores para el manejo de autenticación:

```php
// Mostrar formulario de registro
$router->get('/register', 'registration/create.php')->only('guest');
// Guardar los datos del nuevo usuario
$router->post('/register', 'registration/store.php')->only('guest');

// Mostrar formulario de login
$router->get('/login', 'session/create.php')->only('guest');
// Procesar el formulario de login
$router->post('/session', 'session/store.php')->only('guest');
// Solicitar el cierre de sesión
$router->delete('/session', 'session/destroy.php')->only('auth');
```

También es necesario notar que las rutas pueden tener asignados los [middlewares](routing_middlewares.md) de sesión que permiten restringir el acceso a ciertas zonas de la aplicación a aquellos usuarios que no tienen una sesión activa.

## Clase Session

La clase `Core\Session` es la encargada de abstraer la implementación de sesiones mediante `$_SESSION`, para lo cual provee una serie de métodos con los cuales podemos interactuar.

### Agregar datos a la sesión

Si el proceso de autenticación finaliza exitosamente, se agregan datos relativos al usuario al objeto de sesión. Dentro de `Core\Session` contamos con los métodos que nos permiten realizar esta tarea:

```php
// Comprobar si la sesión posee un dato puntual
public static function has($key): bool
{
    return (bool) static::get($key);
}

// Agregar un nuevo dato a la sesión
public static function put($key, $value)
{
    $_SESSION[$key] = $value;
}

// Leer el valor de un dato de la sesión
public static function get($key, $default = null)
{
    return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
}
```

Nótese el último método _get()_ que devuelve un dato según la siguiente lógica:

1. Si existe un objeto que empiece con la clave "\_flash" y luego el nombre de la clave buscada, devuelve dicho objeto.
2. Sino, si existe un objeto en la sesión con el nombre de la clave buscada, devuelve dicho objeto.
3. Sino, si definimos un valor por defecto a devolver en caso de no encontrar nada, devolverá ese valor.
4. Sino devolverá el valor `null`.

¿Cuál es la diferencia entre agregar "\_flash" como primera clave en sesión? Este tipo de mensajes son útiles para el siguiente contexto.

### Agregar datos temporales

Los datos temporales son aquellos que persisten durante el [ciclo de vida](ciclo_vida.md) de un pedido HTTP y luego son eliminados del objeto `$_SESSION`. Esto es útil, por ejemplo, para devolver a un formulario los errores que hayan surgido durante la validación de datos y los propios datos ingresados al formulario para volver a popularlo.

En `Core\Session`, además del ya mencionado método _get()_ contamos con los siguientes:

```php
// Agregar un nuevo dato temporal a la sesión
public static function flash($key, $value)
{
    $_SESSION['_flash'][$key] = $value;
}

// Eliminar todos los datos de sesión temporales
public static function unflash()
{
    unset($_SESSION['_flash']);
}
```

Si echamos un vistazo a `public/index.php` veremos que al final encontramos el mapeo de una ruta con su respectivo controlador, pero si en este proceso ocurre algún error (por ejemplo, en el controlador falla la validación) el bloque catch asigna los errores temporales y se redirige a la URL anterior, la cual puede mostrar estos mensajes. La última línea procede a eliminar todos los datos de sesión temporales para asegurarse de que se vean solamente una vez.

```php
try {
    $router->route($uri, $method);
} catch (ValidationException $exception) {
    Session::flash('errors', $exception->errors);
    Session::flash('old', $exception->old);

    return redirect($router->previousUrl());
}

Session::unflash();
```

Una forma práctica de acceder a los datos ingresados previamente en un formulario es mediante el método _old()_ que se encuentra en `Core\functions`: basta con pasar el nombre del error generado para obtener el valor anterior.

```php
// functions.php
function old(string $key, string $default = ''): string
{
    return \Core\Session::get('old')[$key] ?? $default;
}
```

```html
<!-- Ejemplo de uso en una vista -->
<input id="email" name="email" type="email" value="<?= old('email') ?>" />
```

### Eliminar datos de sesión

Los últimos métodos permiten eliminar todos los datos de sesión, lo cual se realiza cuando el usuario solicita cerrar su sesión activa en la aplicación.

```php
// Vaciar la variable de sesión en PHP
public static function flush()
{
    $_SESSION = [];
}

public static function destroy()
{
    static::flush();
    session_destroy();

    // Es necesario también eliminar la cookie que quedó almacenada en el navegador
    $params = session_get_cookie_params();
    setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
```

> [!WARNING]
> Recordemos que el llamado a cualquier de estos dos métodos debe realizarse en su debido contexto, ya que elimina todos los datos guardados en la superglobal $\_SESSION.

## Métodos de utilidad

El archivo `Core\functions` posee algunos métodos utilitarios para acceder a ciertos datos de la sesión. Se pueden agregar otras funciones que realicen otro tipo de comprobaciones en el caso de ser necesario.

```php
// Devuelve todos los datos del usuario autenticado
// Esto depende de qué datos se hayan guardado en el proceso de login
function user(): array|false
{
    return $_SESSION['user'] ?? false;
}

// Devuelve el valor del tipo de usuario,
// asumiendo que en la tabla sea un número entero
function user_type(): int
{
    return (int)($_SESSION['user']['type']);
}

// Comprueba si el usuario es del tipo super administrador
function isSuperAdmin(): bool
{
    return user_type() === 1;
}
```

Todas estas funciones pueden ser usadas en los controladores o las vistas.
