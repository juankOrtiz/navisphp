<?php

use Core\App;
use Core\Database;

$db = App::resolve(Database::class);

$config = require base_path('config.php');
$db = new Database($config['database']);

$notes = $db->query("select * from notes where user_id = 1")->get();

view("notes/index.view.php", [
  'heading' => 'My notes',
  'notes' => $notes
]);
