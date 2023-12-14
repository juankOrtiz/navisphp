<?php

use Core\Validator;
use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$userId = 1;

$note = $db->query("select * from notes where id = :id", [
    ':id' => $_POST['id']
])->findOrFail();

authorize($note['user_id'] === $userId);

$errors = [];

if(!Validator::string($_POST['body'], 1, 1000)) {
    $errors['body'] = 'You must enter a body between 1 and 1000 characters';
}

if(count($errors)) {
    return view("notes/edit.view.php", [
        'heading' => 'Edit note',
        'errors' => $errors,
        'note' => $note
    ]);
}

$db->query("UPDATE notes SET body = :body WHERE id = :id", [
    'body' => $_POST['body'],
    'id' => $_POST['id']
]);

header('location: /notes');
die();
