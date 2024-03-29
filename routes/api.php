<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NewspaperController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/newspapers', [NewspaperController::class, 'store']);
// Añadir un periodico con post
// http://localhost:8000/api/newspapers
// {
//     "url": "https://www.heraldodiariodesoria.es/"
//     "urlrss":"https://www.heraldodiariodesoria.es/rss/home.xml"
// }

Route::delete('/newspapers/{id}', [NewspaperController::class, 'destroy']);
//http://localhost:8000/api/newspapers/3
//Eliminar un periodico
// {
//     "name": "Nuevo Nombre del Periódico",
//     "url": "http://nuevaurl.com"
// }

Route::put('/newspapers/{id}', [NewspaperController::class, 'update']);
//Modificar periodico
//http://localhost:8000/api/newspapers/2

Route::post('/newspapers/{userId}/subscribe/{newspaperId}', [NewspaperController::class, 'subscribe']);
// Añadir un periodico a un usuario
// http://localhost:8000/api/newspapers/1/subscribe/1

Route::post('/newspapers/{userId}/unsubscribe/{newspaperId}', [NewspaperController::class, 'unsubscribe']);
// Eliminar el periodico asignado del usuario 
// http://localhost:8000/api/newspapers/1/unsubscribe/1


Route::get('/newspapers/{newspaperId}/checkheadlines', [NewspaperController::class, 'checkHeadlines']);
Route::get('/newspapers/{newspaperId}/headlines', [NewspaperController::class, 'getHeadlines']);
// Mostrar titulares de un periodico
// http://localhost:8000/api/newspapers/1/headlines

Route::get('/newspapers/headlines', [NewspaperController::class, 'getAllHeadlines']);
//Recuperar los titulares de todos los periodicos
// http://localhost:8000/api/newspapers/headlines


Route::get('/newspapers/{id}', [NewspaperController::class, 'show']);
//Recuperar datos de un periodico
//http://localhost:8000/api/newspapers/1
Route::get('/newspapers', [NewspaperController::class, 'index']);
//mostrar todos los periodicos
//http://localhost:8000/api/newspapers

Route::middleware('auth:api')->group(function () {
    Route::get('/user/newspapers', [UserController::class, 'getSubscribedNewspapers']);
});