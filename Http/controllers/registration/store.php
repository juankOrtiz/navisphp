<?php

use Core\App;
use Core\Database;
use Core\Validator;

$email = $_POST['email'];
$password = $_POST['password'];

$errors = [];
if(! Validator::email($email)) {
    $errors['email'] = 'Please enter a valid email address';
}

if(! Validator::string($password, 7, 255)) {
    $errors['password'] = 'Please enter a password between 7 and 255 characters';
}

if(!empty($errors)) {
    return view('registration/create.view.php', [
        'errors' => $errors
    ]);
}

$db = App::resolve(Database::class);
$user = $db->query('select * from users where email = :email', [
    'email' => $email
])->find();

if($user) {
    header('location: /');
    exit();
} else {
    $db->query('insert into users(email, password) VALUES(:email, :password)', [
        'email' => $email,
        'password' => password_hash($password, PASSWORD_BCRYPT)
    ]);

    login($user);

    header('location: /');
    exit();
}