# NavisPHP

> Framework minimalista y moderno de PHP

## Motivación 💬

NavisPHP surge bajo la necesidad de encontrar una estructura de proyecto estable y minimalista para mis proyectos personales. Utiliza como base el código de la serie [PHP for Beginners](https://www.youtube.com/watch?v=dVttuOjew3E) de la plataforma Laracasts, con algunas modificaciones agregadas para satisfacer las demandas propias de mis proyectos.

## Características 📌

-   Moderno: el framework utiliza features actuales como containers, enrutamiento, middlewares, autocarga de clases, entre otros. A su vez, el código utiliza características actuales de PHP como atributos de solo lectura, tipado de argumentos y de retorno de funciones y anotaciones.
-   Minimalista: incluye solo lo básico para ejecutar tu aplicación.

> [!WARNING]
> Este proyecto es un trabajo en proceso; por ende puede contener bugs y no se sugiere utilizarlo en producción

## Instalación 💻

### Requisitos previos

-   [PHP 8.2](https://www.php.net/) (versión mínima recomendada)
-   [Composer](https://getcomposer.org/)
-   [Node.js](https://nodejs.org)
-   _(Opcional)_ [MySQL](https://www.mysql.com/), [MariaDB](https://mariadb.org/) u otro motor de base de datos.

### Pasos a seguir

1. Clonar el repositorio:

```sh
git clone https://github.com/juankOrtiz/navisphp.git
```

2. Abrir una consola y navegar hasta la ruta donde está instalado el proyecto, y luego ejecutar el comando para instalar las dependencias de PHP:

```sh
composer install
```

3. Ejecutar en consola el comando para instalar las dependencias del frontend:

```sh
npm install
```

4. Hacer una copia del archivo `config.example.php`, renombrarlo como `config.php` y modificar las credenciales correspondientes.

5. Controlar en el archivo `php.ini` del servidor web que las extensiones de PDO se encuentren habilitadas

6. Utilizar el siguiente comando para levantar el servidor de desarrollo:

```sh
php -S localhost:8888 -t public
```

7. Visitar la ruta `http://localhost:8888` en un navegador web.

8. _(Opcional)_ Si se desea utilizar TailwindCSS como librería de CSS, ejecutar el siguiente comando en consola para analizar los estilos:

```sh
npm run dev
```

## Documentación 📄

Puedes consultar la documentación del proyecto en [este enlace](docs/introduccion.md).

## Estructura del proyecto 📁

El proyecto utiliza una estructura de carpetas para agrupar su contenido, entre las cuales se encuentran:

-   **root**: archivos de configuración, dependencias y rutas de la aplicación.
-   **Core**: base de la aplicación.
    -   **Middleware**: middlewares que utiliza la aplicación.
    -   **Util**: clases de utilidad, se puede extender con las clases utilitarias necesarias.
-   **Http**: archivos que interactúan con los pedidos Http.
    -   **controllers**: carpeta de los controladores de la aplicación, que se agrupan por funcionalidad.
    -   **Forms**: archivos encargados de la validación de cada formulario presente en la aplicación.
-   **node_modules**: carpeta con las dependencias del frontend, instaladas por NPM.
-   **public**: carpeta pública que se levanta con el servidor, incluyendo el archivo _index.php_ que sirve como punto de entrada.
    -   **css**: archivos CSS utilizados por las vistas.
    -   **img**: imágenes del proyecto.
    -   **js**: archivos JS utilizados por las vistas.
-   **src**: carpeta con los recursos del frontend que se usan en desarrollo (código fuente sin minificar).
    -   **css**: archivos CSS.
    -   **js**: archivos JS.
-   **tests**: carpeta con pruebas de la aplicación.
-   **vendor**: carpeta con las dependencias del backend, instaladas por Composer.
-   **views**: carpeta con las vistas de la aplicación, agrupadas por funcionalidad.
    -   **partials**: carpeta que posee las vistas 'parciales', aquellas utilizadas para construir las partes comunes de cada vista.

## Tecnologías y dependencias generales 🔧

A continuación se listan algunas de las tecnologías y dependencias más importantes del proyecto:

| Nombre                                        | Utilidad                      | Alternativas                                         |
| --------------------------------------------- | ----------------------------- | ---------------------------------------------------- |
| [PestPHP](https://pestphp.com/)               | Suite de testing de PHP       | PHPUnit                                              |
| [TailwindCSS](https://tailwindcss.com/)       | Librería de utilidades de CSS | Se puede remplazar por vanilla CSS u otras librerías |
| [Monolog](https://github.com/Seldaek/monolog) | Librería de logging           | Analog o cualquier librería de logging               |

## Licencia 📜

NavisPHP se rige bajo la [licencia MIT](LICENSE).

## Autores 👥

-   **Juan Carlos Ortiz** - [Perfil de Github](https://github.com/juankOrtiz) - _Programación, Desarrollo Frontend y Backend, Testing_
