<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $t = 8;
    return view('welcome');
});
