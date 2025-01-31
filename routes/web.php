<?php

use App\Models\User;
use Livewire\Volt\Volt;

Volt::route('/', 'auth.login')->name('login');
Volt::route('create/account', 'auth.createaccount')->name('create.account');


Route::middleware('auth')->group(function () {
    Volt::route('verfication/{id}', 'emailverification')->name('email.verification');
    Volt::route('home', 'emailgen')->name('email.gen');
});

