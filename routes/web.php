<?php

use App\Http\Controllers\Api\NewspaperController;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



Route::post('/newspapers/subscribe', [NewspaperController::class, 'subscribeButton']);
// Añadir un periodico a un usuario

Route::post('/newspapers/{userId}/unsubscribe/{newspaperId}', [NewspaperController::class, 'unsubscribeButton']);
// Eliminar el periodico aasignado del usuario 


Route::get('/newspapers/{newspaperId}/headlines', [NewspaperController::class, 'getHeadlinesButton']);
// Mostrar titulares de un periodico

Route::get('/newspapers/headlines', [NewspaperController::class, 'getAllHeadlinesButton']);
//Recuperar los titulares de todos los periodicos
