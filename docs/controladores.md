# Controladores

-   [Introduccion](#introduccion)
-   [Crear un controlador](#crear-un-controlador)
-   [Controladores de recursos](#controladores-de-recursos)
-   [Acciones comunes](#acciones-comunes)

## Introduccion

Los controladores cumplen con la función de responder ante las consultas generadas sobre una ruta. Para cada ruta registrada en la aplicación debe haber un controlador que defina qué acciones deben realizarse.

## Crear un controlador

Para crear un controlador se debe crear un archivo en la carpeta `Http\controllers` y se pueden agrupar los controladores en carpetas que correspondan con un recurso específico. Por ejemplo: NavisPHP agrupa todos los controladores que se encargan de interactuar con la creación, almacenamiento y eliminación de una sesión en la carpeta `session`, y dentro de la misma se definen 1 controlador por cada una de dichas acciones.

Además es preciso definir en el archivo `routes.php` el mapeo de cada ruta con su respectivo controlador. Al mapear el controlador con la ruta solamente debemos agregar el nombre del controlador dentro de la carpeta `Http/controllers`.

```php
// Al dirigirnos a la ruta /login se ejecutará el controlador Http/controllers/session/create.php
$router->get('/login', 'session/create.php')->only('guest');
```

Es importante aclarar que desde que visitamos la ruta definida y antes de llegar hasta el controlador, el pedido HTTP debe cumplir el [ciclo de vida de la aplicación](ciclo_vida.md), el cual se asegura de que se cumplan todos los chequeos previos, como ser la resolución del [middleware](routing_middleware.md) que aparece en este ejemplo.

### Controladores de recursos

Una recomendación a la hora de crear la estructura de controladores es utilizar el patrón de "controladores de recursos", el cual consiste en definir una carpeta con el nombre del recurso y dentro de la misma los nombres de cada controlador que se corresponda con cada una de las 7 acciones estándar.

| Acción                                  | Método HTTP | Nombre del controlador |
| --------------------------------------- | ----------- | ---------------------- |
| Listado del recurso                     | GET         | index.php              |
| Ver detalles de un elemento             | GET         | show.php               |
| Ver formulario de creación              | GET         | create.php             |
| Guardar formulario / crear recurso      | POST        | store.php              |
| Ver formulario de edición               | GET         | edit.php               |
| Guardar formulario / actualizar recurso | PUT/PATCH   | update.php             |
| Eliminar recurso                        | DELETE      | destroy.php            |

## Acciones comunes

Si bien en un controlador tenemos la posibilidad de realizar diferentes tipos de acciones, listamos algunas de las más comunes que podemos encontrar en una aplicación:

1. Obtener datos de una consulta, URL, formulario o consulta AJAX

```php
// Para los métodos POST, PUT, PATCH y DELETE
$body = $_POST['body'];

// Para los datos enviados por URL
$id = $_GET['id'];

// Para los datos enviados por AJAX, en este caso mediante fetch() de JS
header("Content-Type: application/json");
$params = json_decode(file_get_contents('php://input'), true);

$id = $params['id'];
```

2. Validar los datos de una consulta mediante una [clase del tipo Form](validaciones.md)

```php
// La clase LoginForm define cuales son las reglas para que un email y una contraseña sean válidas
$form = LoginForm::validate($attributes = [
    'email' => $_POST['email'],
    'password' => $_POST['password'],
]);
```

3. Hacer consultas a una base de datos, utilizando la clase Database y cada uno de sus métodos.

```php
$db = App::resolve(Database::class);

$user = $db->query(
    "SELECT *
    FROM users
    WHERE idusers = :id",
    [
        'id' => $id
    ]
)->find();
```

4. Comprobar si un usuario tiene permisos para realizar las tareas del controlador mediante el método _autorize()_

```php
// Ejemplo: controlador que permite editar el contenido de una nota
// Se comprueba si el usuario actual es el dueño de la nota, caso contrario no debería editarla
authorize($note['user_id'] === $userId);
```

5. Devolver una [vista](vistas.md) al usuario

```php
view("users/create.view.php", [
  'heading' => 'Create user',
  'errors' => []
]);
```
