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
    # Sample update data pada database
*/
$sample_1 = $simple_crud->update('users', ['username' => 'Ranzz'], ['id' => 1]);
var_dump($sample_1);