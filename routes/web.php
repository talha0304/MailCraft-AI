<?php

use Livewire\Volt\Volt;

Volt::route('/', 'auth.login')->name('login');
Volt::route('create/account', 'auth.createaccount')->name('create.account');
Volt::route('verfication/{id}', 'emailverification')->name('email.verification');


Route::middleware('auth')->group(function () {
    Volt::route('home', 'emailgen')->name('email.gen');
});

