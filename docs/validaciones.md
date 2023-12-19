# Validaciones

<< Anterior: [Bases de datos](database.md)

\>> Siguiente: [Sesiones y autenticación](sesiones_autenticacion.md)

-   [Introduccion](#introduccion)
-   [Forms](#forms)
-   [Clase Validator](#clase-validator)

## Introduccion

Una parte importante de todo flujo de aplicación es la validación de los datos enviados a los controladres, ya sea mediante formularios u otros medios. La falta de validación puede incurrir en distintas problemáticas, como ser el procesamiento de datos con formato erróneo, la eliminación de datos cruciales o incluso la inyección de código malicioso en la aplicación.

## Forms

En NavisPHP la carpeta `Http\Forms` contiene las clases de validación que pueden utilizarse en cada controlador para validar los datos que recibe antes de continuar con acciones más sensibles, como la inserción de datos en una base de datos. El nombre de este tipo de clases está motivado por el hecho de que la mayoría de los datos enviados a un controlador se realizan mediante formularios en las vistas, pero se pueden definir clases de validación para datos que provengan de otros medios, como ser datos pasados por URL, por consultas AJAX desde el frontend o mediante consultas a una API.

Una clase de validación debe heredar de la clase base `Form`, sin la necesidad de sobrescribir ningún método, salvo el constructor. En el método constructor se definen cuales son los datos a validar y que regla de validación se aplicará para cada uno de ellos.

Veamos como ejemplo la clase `LoginForm.php` que valida los datos ingresados al formulario de Login.

```php
namespace Http\Forms;

use Core\Validator;

class LoginForm extends Form
{
    public function __construct(public array $attributes)
    {
        // Si el atributo email no cumple con la regla de validación de un email
        // Agregarmos un nuevo error al arreglo de errores
        if(!Validator::email($attributes['email'])) {
            $this->errors['email'] = 'Please enter a valid email';
        }

        // Mismo caso para la contraseña, pero con una regla de validación distinta
        if(!Validator::string($attributes['password'])) {
            $this->errors['password'] = 'Please enter a valid password';
        }
    }
}
```

Para utilizar esta clase, basta con crear una nueva instancia de la misma con un arreglo de los datos a validar:

```php
$form = LoginForm::validate($attributes = [
    'email' => $_POST['email'],
    'password' => $_POST['password'],
]);
```

En el caso de que al menos una de las validaciones sea incorrecta, se ejecutará automáticamente el método _throw()_ de la clase Form que arrojará un nuevo error de la clase `ValidationException`.

## Clase Validator

Cada uno de los tipos de validaciones que pueden usar los constructores de las clases Form están definidos en la clase `Core\Validator`. Cada tipo de validación es una función estática que define las reglas para que un valor sea válido o no.

Veamos como ejemplo las dos funciones utilizadas para validar las credenciales de login:

```php
public static function string($value, $min = 1, $max = INF): bool
{
    // Quitar los espacios blancos de los extremos de la cadena
    $value = trim($value);

    // Comprobar si la longitud de la cadena es mayor que el mínimo y mayor que el máximo
    return strlen($value) >= $min && strlen($value) <= $max;
}

public static function email($value)
{
    // Utilizar el método nativo filter_var para comprobar el formato de email
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}
```

Si nos encontramos con un nuevo tipo de validación que no existe, podemos agregarlo a este archivo como una nueva función estática y luego llamarlo desde el constructor de nuestras clases Form.

[Volver al inicio](#validaciones)

<< Anterior: [Bases de datos](database.md)

\>> Siguiente: [Sesiones y autenticación](sesiones_autenticacion.md)
