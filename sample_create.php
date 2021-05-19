<?php
/*
    # Memanggil file library Simple Crud
*/
require __DIR__ . '/Simple_CRUD.php';

/*
    # Inisalisasi class library
*/
$simple_crud = new Simple_CRUD([
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'test',
    'is_debug' => TRUE
]);

/*
    # Sample insert data sederhana
*/
$sample_1 = $simple_crud->insert('users', [
    'username' => 'Ikbal'
]);
var_dump($sample_1);
echo "<br><br>";

/*
    # Sample insert multi data (batch)
    # Silahkan gunakan array 2 dimensi pada parameter data
*/
$sample_2 = $simple_crud->insert('users', [
    ['username' => 'Yasmin'],
    ['username' => 'Fauzan']
]);
var_dump($sample_2);
echo "<br><br>";