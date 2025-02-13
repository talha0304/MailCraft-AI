<?php

use Livewire\Volt\Volt;

Volt::route('/', 'pages.intropage')->name('email.verification');

Volt::route('login', 'auth.login')->name('login');
Volt::route('create/account', 'auth.createaccount')->name('create.account');

Volt::route('verfication', 'pages.verfiaction.emailverification')->name('email.verification');

Volt::route('resetpassword', 'pages.forgetpassword')->name('forget.password');


Route::middleware('auth')->group(function () {
    Volt::route('dashboard', 'pages.dashboard')->name('dashboard');
    Volt::route('home', 'pages.emailgen')->name('email.gen');
    Volt::route('languages/add', 'languages.add')->name('add.lang');
    Volt::route('languages/update/{id}', 'languages.update')->name('update.lang');
    Volt::route('languages/show', 'languages.show')->name('show.lang');
});

