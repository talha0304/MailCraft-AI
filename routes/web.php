<?php

use Livewire\Volt\Volt;

Volt::route('/', 'pages.intropage')->name('email.verification');
Volt::route('login', 'auth.login')->name('login');
Volt::route('create/account', 'auth.createaccount')->name('create.account');
Volt::route('company/create/account','auth.registercompany')->name('create.company.account');
Volt::route('verfication/{id}', 'pages.emailverification')->name('email.verification');


Route::middleware('auth')->group(function () {
    Volt::route('home', 'pages.emailgen')->name('email.gen');
});

