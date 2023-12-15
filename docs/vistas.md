# Vistas

-   [Introduccion](#introduccion)
-   [Características de las vistas](#características-de-las-vistas)
-   [Vistas parciales](#vistas-parciales)
-   [Páginas](#páginas)
-   [Ejemplo de vista completa](#ejemplo-de-vista-completa)

## Introduccion

Las vistas son los elementos visuales de la aplicación, compuestas por archivos PHP que incluyen código HTML para mostrar la información de la aplicación al usuario final.

## Características de las vistas

Las vistas se almacenan en la carpeta `views`, directorio desde el cual son accesibles a los [controladores](controladores.md) mediante el método _view()_. Este método permite enviar variables a las vistas desde los controladores, las cuales serán accesibles en las vistas por más que estas no las hayan definido en su cuerpo.

Existen dos tipos generales de vistas que veremos a continuación.

## Vistas parciales

También conocidos como "partials", son archivos que contienen fragmentos de código HTML o PHP que representan parte de una vista o de un componente. Suelen crearse con el objetivo de ser reutilizados en las páginas. NavisPHP define la carpeta `views\partials` para almacenar este tipo de archivo, y el único requisito es que la extensión del mismo sea `.php`. En el siguiente ejemplo vemos el partial `head.php` que define el encabezado del código HTML que queremos que esté presente en todas las vistas:

```html
<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
    <head>
        <meta charset="UTF-8" />
        <meta
            name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
        />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>NavisPHP</title>
        <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    </head>
    <body class="h-full">
        <div class="min-h-full"></div>
    </body>
</html>
```

Para cargar un partial en otra página usamos la siguiente sintáxis:

```php
<?php require base_path('views/partials/head.php') ?>
```

O también podemos definir una función en nuestro archivo `functions.php` que permita simplificar dicha sintáxis.

Notemos también que este tipo de vistas no pueden ser invocados mediante el método _view()_ mencionado previamente.

## Páginas

Las páginas suelen contener el código HTML y PHP necesario para mostrar el contenido exclusivo de cada vista, aquel que no se comparte con otras páginas. Para definir una página es necesario que la extensión del archivo sea `.view.php`, requisito para que el método _view()_ pueda cargar y mostrar la vista de forma apropiada.

Además de estas consideraciones, no existe mucha diferencia entre este tipo de vista y una página de contenido común de PHP o HTML, de modo que dentro de una página podremos:

-   Cargar una vista parcial en cualquier parte de la página.
-   Cargar archivos CSS o definir estilos mediante la etiqueta `<style>`.
-   Cargar archivos JS o definir código mediante la etiqueta `<script>`.
-   Acceder a cualquier variable que sea enviada mediante el controlador de la vista.
-   Usar código PHP en cualquier momento al usar las etiquetas `<php ?>`.
-   Imprimir variables o cadenas de PHP mediante las etiquetas `<?= ?>`.

## Ejemplo de vista completa

Veamos a continuación un ejemplo sencillo de una página que muestra un listado de usuarios. Primero, definimos en el controlador el método que envía los datos a la vista:

```php
view('users/index.view.php', [
  'users' => $users
]);
```

Luego, veamos el contenido de la propia vista:

```php
// views/users/index.view.php

// Cargar los partials necesarios
<?php require base_path('views/layouts/head.php') ?>
<?php require base_path('views/layouts/body.php') ?>
<?php require base_path('views/layouts/nav.php') ?>

// Definir estilos personalizados
<style>
    .dash-main-div {
        margin-top: 20px;
    }
</style>

<div class="dash-main-div">
    <main class="content_padding">
        <div>
            <?php if (count($users) === 0) : ?>
                <p>No existen usuarios registrados.</p>
            <?php else : ?>
                <table id="table-usuarios">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Email</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        // Evaluar el arreglo $users enviado mediante el controlador
                        <?php foreach ($users as $usuario) : ?>
                            <tr data-enabled="<?= $usuario['estado'] === 1 ? '1' : '0' ?>">
                                <td>
                                    <?= $usuario['idusers'] ?>
                                </td>
                                <td>
                                    <?= $usuario['name'] ?>
                                </td>
                                <td>
                                    <?= $usuario['email'] ?>
                                </td>
                                <td>
                                    <a class="a-ver-informacion hover" href="/users/show">Ver detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require base_path('views/layouts/footer.php') ?>

// Agregar script de JS
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let pathname = window.location.pathname;

        if (pathname === "/users") {
            console.log("Pagina de usuarios");
        }
    }
</script>
```
