<?php

use App\Events\DeclarationEvent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/event', function () {
//     return view('notif');
// });

// Route::post('/event', function () {
//     $name = request()->name;

//     event(new DeclarationEvent($name));
// });
