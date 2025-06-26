<?php

use Illuminate\Support\Facades\Route;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tab/{transaksiSimpananBerjangka}', function (App\Models\PembukaanRekeningBaru $transaksiSimpananBerjangka) {
    return view('form_tab', ['tab' => $transaksiSimpananBerjangka]);
})->name('form_tab');


Route::get('/forgot-password', RequestPasswordReset::class)
    ->middleware('guest')
    ->name('password.request');

Route::get('/reset-password/{token}', ResetPassword::class)
    ->middleware('guest')
    ->name('password.reset');
