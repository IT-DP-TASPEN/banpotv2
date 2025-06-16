<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tab/{transaksiSimpananBerjangka}', function (App\Models\PembukaanRekeningBaru $transaksiSimpananBerjangka) {
    return view('form_tab', ['tab' => $transaksiSimpananBerjangka]);
})->name('form_tab');