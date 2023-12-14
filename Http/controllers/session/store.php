<?php

use Core\Authenticator;
use Http\Forms\LoginForm;

$email = $_POST['email'];
$password = $_POST['password'];

$form = LoginForm::validate($attributes = [
    'email' => $_POST['email'],
    'password' => $_POST['password'],
]);

$signedIn = (new Authenticator)->atempt(
    $attributes['email'],
    $attributes['password']
);

if(! $signedIn) {
    $form->error('email', "There's not account with those credentials")
        ->throw();
}

redirect('/');
