<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('Auth', 'Auth::index'); // Untuk menampilkan halaman login
$routes->post('Auth/login', 'Auth::login'); // Untuk memproses login
$routes->get('Auth/logout', 'Auth::logout'); // Untuk logout


//Admin
$routes->get('Admin', 'Admin::index');
$routes->get('Admin/Produk', 'Admin::Produk');
$routes->get('Admin/tambahProduk', 'Admin::tambahProduk');
$routes->get('Admin/hapusProduk(:num)', 'Admin::hapusProduk/$1');
$routes->get('Admin/editProduk(:num)', 'Admin::editProduk/$1');
$routes->post('Admin/simpanProduk', 'Admin::simpanProduk');
$routes->post('Admin/updateProduk(:num)', 'Admin::updateProduk/$1');
$routes->get('Admin/Laporan', 'Admin::Laporan');
$routes->post('Admin/simpanPenjualan', 'Admin::simpanPenjualan');
$routes->get('Admin/laporan/detail/(:num)', 'Admin::detailPenjualan/$1');
$routes->get('Admin/hapusPenjualan(:num)', 'Admin::hapusPenjualan/$1');
$routes->get('Admin/laporan/filter', 'Admin::filterPenjualan');
$routes->get('Admin/laporan/cetak/(:num)', 'Admin::cetakStruk/$1');
$routes->get('Admin/Pengguna', 'Admin::Pengguna');

//User
$routes->get('User', 'User::index');
$routes->get('User/Produk', 'User::Produk');
$routes->get('User/tambahProduk', 'User::tambahProduk');
$routes->get('User/Transaksi', 'User::Transaksi');
$routes->post('User/simpanPenjualan', 'User::simpanPenjualan');
$routes->get('User/laporan/detail/(:num)', 'User::detailPenjualan/$1');
$routes->get('User/hapusPenjualan(:num)', 'User::hapusPenjualan/$1');
$routes->get('User/laporan/cetak/(:num)', 'User::cetakStruk/$1');
$routes->get('User/laporan/filter', 'User::filterPenjualan');