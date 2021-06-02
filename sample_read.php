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
    # Sample read data sederhana
    # Select data pada 1 tabel
*/
$sample_1 = $simple_crud->get_rows('users');
var_dump($sample_1);
echo "<br><br>";

/*
    # Sample read data menggunakan kondisi where
    # Select data pada 1 tabel
*/
$sample_2 = $simple_crud->get_rows('users', [
    'where' => ['id' => 1]
]);
var_dump($sample_2);
echo "<br><br>";

/*
    # Sample read data menggunakan fitur join
    # Select data pada 2 tabel
*/
$sample_3 = $simple_crud->get_rows('users', [
    'join' => ['table' => 'users_detail', 'on' => 'users_detail.id = users.id', 'param' => 'LEFT']
]);
var_dump($sample_3);
echo "<br><br>";

/*
    # Sample read data untuk lebih dari satu data
    # Select data pada 1 tabel
*/
$sample_4 = $simple_crud->get_rows('users', [], 'array', TRUE);
var_dump($sample_4);
echo "<br><br>";

/*
    # Sample read data menggunakan fitur order by
    # Select data pada 1 tabel
*/
$sample_5 = $simple_crud->get_rows('users', [
    'order_by' => ['type' => 'DESC', 'column' => 'id'],
    'return' => 'array',
    'is_indexed' => TRUE
]);
var_dump($sample_5);
echo "<br><br>";

/*
    # Sample read data menggunakan fitur group by
    # Select data pada 1 tabel
*/
$sample_6 = $simple_crud->get_rows('users', [
    'group_by' => 'id',
    'return' => 'array',
    'is_indexed' => TRUE
]);
var_dump($sample_6);
echo "<br><br>";

/*
    # Sample read data menggunakan fitur limit
    # Select data pada 1 tabel
*/
$sample_7 = $simple_crud->get_rows('users', [
    'limit' => ['limit' => 1],
    'return' => 'array',
    'is_indexed' => TRUE
]);
var_dump($sample_7);
echo "<br><br>";