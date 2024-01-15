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

Route::get('siembras/listado-lotes', 'API\SiembraController@listadoLotes');
Route::get('siembras/listado', 'API\SiembraController@listadoSiembras');
Route::get('siembras/campos', 'API\SiembraController@campos');

Route::apiResources([
	'contenedores' => 'API\ContenedorController',
	'actividades' => 'API\ActividadController',
	'alimentos' => 'API\AlimentosController',
	'recursos' => 'API\RecursosController',
	'especies' => 'API\EspeciesController',
	'usuarios' => 'API\UsuarioController',
	'siembras' => 'API\SiembraController',
	'registros' => 'API\RegistroController',
	'recursos-necesarios' => 'API\RecursoNecesarioController',
	'recursos-siembras' => 'API\RecursoSiembraController',
	'informes' => 'API\InformeController',
	'parametros-calidad' => 'API\ParametroCalidadController',
	'informes-siembras' => 'API\InformeSiembraController',
	'informes-registros' => 'API\InformeRegistroController',
	'informes-recursos-necesarios' => 'API\InformeRecursosNecesariosController',
	'informes-alimentos' => 'API\InformeAlimentosController',
	'informes-biomasa-alimento' => 'API\InfomeBiomasaAlimentoController',
	'phases' => 'PhaseController',
	'historial-alimentos-costos' => 'API\HistorialAlimentoController',
	'historial-recursos-costos' => 'API\HistorialRecursoController',
	'especies-siembra' => 'API\EspeciesSiembraController',
	'warehouses' => 'WarehouseController',
	'resource-categories' => 'ResourceCategoryController',
	'providers' =>  'ProviderController'
]);

Route::namespace('API')->group(function () {
	Route::post('registros-siembra/{id}', 'RegistroController@registrosxSiembra');
	Route::post('actualizarEstado/{id}', 'SiembraController@actualizarEstado');
	Route::post('siembras/cambiar-estado/{id}', 'SiembraController@changeStatus');
	Route::post('filtro-siembras', 'InformeSiembraController@filtroSiembras');
	Route::post('searchResults', 'RecursoNecesarioController@searchResults');
	Route::post('informe-recursos-totales', 'InformeController@informeRecursosTotales');
	Route::post('filtro-parametros', 'ParametroCalidadController@filtroParametros');
	Route::post('filtro-parametros-excel', 'ParametroCalidadController@filtroParametrosExcel');
	Route::post('parametro-x-contenedor/{id}', 'ParametroCalidadController@mostrarParametrosxContenedores');
	Route::post('parametro-x-contenedor-excel/{id}', 'ParametroCalidadController@mostrarParametrosxContenedoresExcel');
	Route::post('anadir-especie-siembra', 'SiembraController@anadirEspeciesxSiembra');
	Route::post('siembras-alimentacion/{id}', 'RecursoNecesarioController@siembraxAlimentacion');
	Route::get('listadoContenedores', 'ContenedorController@listadoContenedores');
	Route::get('especies-siembra-edita/{id}', 'SiembraController@getEspeciesSiembra');
	Route::get('lista-alimentacion', 'RecursoNecesarioController@alimentacion');
	Route::get('traer-siembras', 'SiembraController@traerSiembras');
	Route::get('traer-existencias', 'InformeController@traerExistencias');
	Route::get('traer-existencias-detalle', 'InformeController@traerExistenciasDetalle');
	Route::get('parametros-contenedores', 'ParametroCalidadController@listadoParametrosContenedores');
});
