<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {

    return $request->user();
});

Route::apiResources([
    'contenedores' => 'API\ContenedorController',
    'alimentos' => 'API\AlimentosController',
    'recursos' => 'API\RecursosController',
    'especies' => 'API\EspeciesController',
    'usuarios' => 'API\UsuarioController',
    'siembras' => 'API\SiembraController',
    'registros' => 'API\SiembraController'
]);

Route::namespace('API')->group(function () {
    Route::get('getPecesSiembras', 'SiembraController@getPecesSiembras');
    Route::post('actualizarEstado/{id}', 'SiembraController@actualizarEstado');
});