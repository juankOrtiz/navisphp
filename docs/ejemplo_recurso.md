# Ejemplo de creación de un recurso

-   [Introduccion](#introduccion)
-   [Descripción del recurso](#descripción-del-recurso)
-   [Base de datos](#base-de-datos)
-   [Definir rutas](#definir-rutas)
-   [Controladores y vistas](#controladores-y-vistas)
-   [Listado](#listado)
-   [Nueva tarea](#nueva-tarea)
-   [Validación de datos](#validación-de-datos)
-   [Creación de la tarea](#creación-de-la-tarea)
-   [Edición de la tarea](#edición-de-la-tarea)
-   [Actualizar la tarea](#actualizar-la-tarea)
-   [Eliminar la tarea](#eliminar-la-tarea)

## Introduccion

En esta sección veremos como crear un recurso paso a paso en NavisPHP. Entendemos por recurso a una acción concreta dentro de una aplicación y al conjunto de elementos componentes lógicos necesarios para lograr dicha funcionalidad.

> [!INFO]
> Para una mayor simplicidad, esta sección prescinde de todo lo relacionado a estilos CSS y scripts de Javascript.

### Descripción del recurso

El ejemplo dado es la creación de un listado de tareas a realizar. Este recurso tiene las siguientes características que deseamos implementar:

-   Cada tarea tendrá un nombre, el usuario al cual pertenece, la fecha de creación y el estado (completado o pendiente).
-   Las opciones de interacción con las tareas serán: ver un listado de las tareas, editar el estado de una tarea, agregar una nueva tarea y eliminar una tarea.
-   Al recurso solo podrán acceder usuarios que estén registrados en la aplicación.
-   Un usuario podrá ver solamente el listado de tareas asignados a sí mismo, pero no podrá interactuar con ninguna tarea de otros usuarios.

## Base de datos

Empezamos creando una nueva tabla en nuestra base de datos con la siguiente estructura:

```sql
CREATE TABLE `tasks` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(150) NOT NULL COLLATE 'utf8mb4_general_ci',
	`created_at` DATETIME NOT NULL,
	`status` TINYINT(4) NOT NULL DEFAULT '0',
	`user_id` INT(11) NOT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	INDEX `user_id` (`user_id`) USING BTREE,
	CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8mb4_general_ci'
ENGINE=InnoDB;
```

> [!INFO]
> Para mantener la consistencia con el resto del código de NavisPHP, el ejemplo utiliza el nombre de la tabla y de los campos en inglés, pero esto es completamente opcional.

## Definir rutas

Dentro de nuestro archivo `routes.php` definimos las rutas que serán necesarias para cubrir todas las acciones del recurso.

```php
$router->get('/tasks', 'tasks/index.php')->only('auth');
$router->get('/tasks/create', 'tasks/create.php')->only('auth');
$router->post('/tasks', 'tasks/store.php')->only('auth');
$router->get('/task/edit', 'tasks/edit.php')->only('auth');
$router->patch('/task', 'tasks/update.php')->only('auth');
$router->delete('/task', 'tasks/destroy.php')->only('auth');
```

El método _only('auth')_ se asegura que todas estas rutas serán accesibles solamente a los usuarios que están registrados.

## Controladores y vistas

Dentro de la carpeta `Http\controllers` creamos una nueva carpeta `tasks` y dentro de la misma vamos agregando cada uno de los controladores necesarios para cada ruta. De igual manera crearemos la carpeta `tasks` dentro de `views` para agrupar las vistas.

### Listado

En el controlador `index.php` buscamos todas las tareas que existen en la tabla y luego devolvemos esa lista a la vista.

```php
<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$tasks = $db->query(
    "SELECT *
    FROM tasks
    WHERE user_id = :id", [
        'id' =>
    ]
)->get();

view("tasks/index.view.php", [
  'tasks' => $tasks
]);
```

La vista `index.view.php` recorrerá la cantida de tareas y mostrará sus datos, además de mostrar un botón para crear una nueva tarea.

```html
<main>
    <div>
        <?php if (count($tasks) === 0) : ?>
        <p>La lista de tareas se encuentra vacía.</p>
        <?php else : ?>
        <ul>
            <?php foreach ($tasks as $task): ?>
            <li>
                <!-- Enlace que redirige al formulario de edición -->
                <a href="/task/edit?id=<?= $task['id'] ?>">
                    <?= htmlspecialchars($task['name']) ?>
                </a>

                <!-- Formulario para eliminar una tarea -->
                <form method="POST" action="/task">
                    <!-- Este campo permite "remplazar" el método POST del form por DELETE -->
                    <input type="hidden" name="_method" value="DELETE" />
                    <input type="hidden" name="id" value="<?= $task['id'] ?>" />
                    <button type="submit">Eliminar</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <p class="mt-6">
            <a href="/tasks/create">Crear una tarea</a>
        </p>
    </div>
</main>
```

### Nueva tarea

En el controlador `create.php` simplemente nos encargaremos de devolver la vista con el formulario para crear una nueva tarea, y una variable de errores que, en un principio, estará vacía.

```php
<?php

view("tasks/create.view.php", [
  'errors' => []
]);
```

La vista `create.view.php` mostrará un formulario con los campos necesarios para crear una nueva tarea, así como también ofrecerá la posibilidad de mostrar los errores introducidos en cada campo (ver siguiente acción).

```html
<main>
    <form method="POST" action="/tasks">
        <div>
            <label for="name">Nombre de la tarea</label>
            <textarea id="name" name="name" rows="3">
			<!-- Si estamos redirigiendo a esta página mediante un error, mostraremos los datos integrados previamente  -->
				<?= $_POST['name'] ?? '' ?>
			</textarea
            >

            <!-- Mostrar error de este campo en caso de existir  -->
            <?php if(isset($errors['name'])): ?>
            <p><?= $errors['name'] ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Crear</button>
    </form>
</main>
```

### Validación de datos

Para validar los datos ingresados en el formulario de creación de la tarea, creamos un el archivo `Http/Forms/tasks/CreateForm.php` y agregamos las reglas de validación, que en este caso consiste solamente en el nombre de la tarea.

```php
<?php

namespace Http\Forms;

use Core\Validator;

class CreateForm extends Form
{
    public function __construct(public array $attributes)
    {
        if(! Validator::string($attributes['name'], 1, 150)) {
            $errors['name'] = 'Debes ingresar una tarea entre 1 y 150 caracteres de longitud';
        }
    }
}
```

### Creación de la tarea

En el controlador `store.php`, al cual accedemos a tomaremos los datos del formulario y los validaremos. Según el resultado de la validación tomaremos dos caminos: si hay algún error, redirigimos a la vista de creación con los errores encontrados; sino, creamos la tarea en la tabla y redirigimos al listado de tareas.

```php
<?php

use Core\App;
use Core\Database;
use Http\Forms\tasks\CreateForm;

$db = App::resolve(Database::class);

$errors = [];

$form = CreateForm::validate($datos = [
    'name' => $_POST['name'],
]);

$db->query(
	"INSERT INTO tasks(name, created_at, status, user_id)
	VALUES(:name, NOW(), '0', :user_id)", [
		'name' => $_POST['name'],
		'user_id' => user()['id'];
	]);

header('location: /tasks');
exit();
```

Notarás que en el código anterior estamos tomando el ID del usuario que ha iniciado sesión. Para que esto funcione tenemos que modificar el método _login()_ de la clase `Core\Authenticator` para agregar dicho dato una vez que el usuario inicia sesión.

```php
public function atempt(string $email, string $password): bool
{
    $user = App::resolve(Database::class)
        ->query(
            "SELECT *
            FROM users
            WHERE email = :email", [
                'email' => $email
            ]
        )->find();

    if($user) {
        if(password_verify($password, $user['password'])) {
            // Modificar el llamado a login para agregar el id
            $this->login([
                'id' => $user['id'],
                'email' => $email,
                'type' => $user['type'],
            ]);

            return true;
        }
    }

    return false;
}

public function login(array $user): void
{
    $_SESSION['user'] = [
        // Agregar id a los datos de la sesión
        'id' => $user['id'],
        'email' => $user['email'],
        'type' => $user['type'],
    ];

    session_regenerate_id(true);
}
```

### Edición de la tarea

Al presionar el formulario de edición en la vista `index.view.php` se llegará al siguiente formulario, cuya tarea adicional es autorizar que el usuario que intenta editar la tarea sea el dueño de la misma. En caso contrario, el método _authorize()_ arrojará un error 404.

```php
<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$task = $db->query(
    "SELECT *
    FROM tasks
    WHERE id = :id", [
        ':id' => $_GET['id']
    ]
)->findOrFail();

authorize($task['user_id'] === user()['id']);

view("tasks/edit.view.php", [
    'errors' => [],
    'task' => $task
]);
```

Como resultado de la ejecución del controlador, se devolverá la vista `edit.view.php`.

```html
<main>
    <form method="POST" action="/task">
        <!-- Este campo permite "remplazar" el método POST del form por PATCH -->
        <input type="hidden" name="_method" value="PATCH" />
        <input type="hidden" name="id" value="<?= $task['id'] ?>" />

        <label for="name">Tarea</label>
        <div>
            <textarea id="name" name="name" rows="3">
                <?= $task['name'] ?>
            </textarea>

            <?php if(isset($errors['name'])): ?>
            <p><?= $errors['name'] ?></p>
            <?php endif; ?>
        </div>

        <label for="status">Estado</label>
        <div>
            <!-- Dependiendo del valor de status, agregamos el atributo checked o no -->
            <input type="checkbox" name="status" id="status"
            <?= $task['status'] === '1' ? 'checked' : '' ?>>
        </div>

        <div>
            <a href="/tasks">Cancelar</a>

            <button type="submit">Actualizar</button>
        </div>
    </form>
</main>
```

### Actualizar la tarea

Al presionar el botón **Actualizar** en el formulario anterior se llegará al controlador `update.php`, el cual también controlará la validación de los campos y la autorización de edición del usuario actual.

```php
<?php

use Core\App;
use Core\Database;
use Http\Forms\tasks\CreateForm;

$db = App::resolve(Database::class);

$task = $db->query(
    "SELECT *
    FROM tasks
    WHERE id = :id", [
        ':id' => $_POST['id']
    ]
)->findOrFail();

authorize($task['user_id'] === user()['id']);

$errors = [];

$form = CreateForm::validate($datos = [
    'name' => $_POST['name'],
]);

$db->query(
    "UPDATE tasks
    SET name = :name,
    status = :status,
    WHERE id = :id", [
        'name' => $_POST['name'],
        'status' => $_POST['status'],
        'id' => $_POST['id']
    ]);

header('location: /tasks');
die();
```

### Eliminar la tarea

Al presionar el botón **Eliminar** en `index.view.php` para una tarea específica, se ejecutará el controlador `destroy.view.php`, el cual autorizará si el usuario tiene permisos para eliminar la tarea y luego la borrará de la base de datos.

Nota que en `index.view.php` no existe una confirmación a la hora de presionar el botón eliminar, lo cual sería lo ideal para prevenir eliminar una tarea por accidente. Esto se puede controlar fácilmente con algunas líneas de Javascript desde el frontend.

```php
<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$task = $db->query(
    "SELECT *
    FROM tasks
    WHERE id = :id", [
        ':id' => $_POST['id']
    ]
)->findOrFail();

authorize($task['user_id'] === user()['id']);

$db->query(
    "DELETE FROM tasks
    WHERE id = :id", [
        ':id' => $_POST['id']
    ]);

header('location: /tasks');
exit();
```
