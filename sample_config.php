<?php
/*
    # Memanggil file library Simple Crud
*/
require __DIR__ . '/Simple_CRUD.php';

/*
    # Inisalisasi class library
    # Berikut keterangan parameter nya:
        - [host] => berisi host database anda
        - [user] => berisi username database anda
        - [pass] => berisi password database anda
        - [is_debug] => optional, silahkan diisi dengan true (boolean) untuk mendapatkan log error jika terdapat kesalahan (pada saat menjalankan query)
*/
$simple_crud = new Simple_CRUD([
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'test',
    'is_debug' => TRUE
]);

/*
    # Menampilkan output berupa nilai pada properti db yang terdapat pada class library
    # Jika tidak ada kesalahan, maka properti ini akan memiliki nilai berupa object
    # Jika properti ini bernilai false (boolean), silahkan cek log error pada server anda untuk melihat bagian mana yang salah
*/
var_dump($simple_crud->db);