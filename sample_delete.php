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
    # Sample delete data menggunakan kondisi
*/
$sample_1 =  $simple_crud->delete('users', ['username' => 'Fauzan']);
var_dump($sample_1);
echo "<br><br>";

/*
    # Sample delete data tanpa kondisi (ini sangat berbahaya karena jika data yang dimiliki ribuan, dan belum di backup. Ya wasalam :))
*/
$sample_2 =  $simple_crud->delete('users_detail');
var_dump($sample_2);
echo "<br><br>";

/*
    # Sample delete seluruh data pada tabel
*/
$sample_3 =  $simple_crud->truncate('users');
var_dump($sample_3);
echo "<br><br>";