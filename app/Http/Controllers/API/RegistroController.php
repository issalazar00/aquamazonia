<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Registro;
use App\EspecieSiembra;

class RegistroController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
		$registros = Registro::select(
			'registros.*',
			'especies.especie as especie',
			'especies.id as id_especie'
		)
			->join(
				'especies',
				'registros.id_especie',
				'especies.id'
			)
			->orderBy('registros.id', 'desc')
			->get();
		return $registros;
	}

	public function registrosxSiembra($id, Request $request)
	{
		$filtroSiembraId = 'registros.id';
		$signoFiltroSiembraId = '!=';
		$valorFiltroSiembraId = '-1';
		$filtroTipoRegistro = 'registros.id';
		$signoFIltroTipoResgistro = '!=';
		$valorFiltroTipoRegistro = '-1';
		$filtroRegistroDesde = 'registros.id';
		$signoFiltroRegistroDesde = '!=';
		$valorFiltroRegistroDesde = '-1';
		$filtroRegistroHasta = 'registros.id';
		$signoFiltroRegistroHasta = '!=';
		$valorFiltroRegistroHasta = '-1';

		if ($request['f_siembra'] != '-1') {
			$filtroSiembraId = "id_siembra";
			$signoFiltroSiembraId = '=';
			$valorFiltroSiembraId = $request['f_siembra'];
		}
		if ($request['search_activity'] != '-1') {
			$filtroTipoRegistro = "tipo_registro";
			$signoFIltroTipoResgistro = '=';
			$valorFiltroTipoRegistro = $request['search_activity'];
		}
		if ($request['search_from'] != '-1') {
			$filtroRegistroDesde = "fecha_registro";
			$signoFiltroRegistroDesde = '>=';
			$valorFiltroRegistroDesde = $request['search_from'];
		}
		if ($request['search_to'] != '-1') {
			$filtroRegistroHasta = "fecha_registro";
			$signoFiltroRegistroHasta = '<=';
			$valorFiltroRegistroHasta = $request['search_to'];
		}

		$registros = Registro::select(
			'registros.id as id',
			'id_siembra',
			'fecha_registro',
			'tipo_registro',
			'peso_ganado',
			'mortalidad',
			'cantidad',
			'estado',
			'biomasa',
			'cantidad',
			'especies.especie as especie',
			'especies.id as id_especie'
		)
			->join(
				'especies',
				'registros.id_especie',
				'especies.id'
			)
			->where($filtroSiembraId, $signoFiltroSiembraId, $valorFiltroSiembraId)
			->where($filtroTipoRegistro, $signoFIltroTipoResgistro, $valorFiltroTipoRegistro)
			->where($filtroRegistroDesde, $signoFiltroRegistroDesde, $valorFiltroRegistroDesde)
			->where($filtroRegistroHasta, $signoFiltroRegistroHasta, $valorFiltroRegistroHasta)
			->orderBy('registros.id', 'desc')
			->with('siembra')
			->get();
			
		return $registros;
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{

		foreach ($request->campos as $campo) {
			$exs = EspecieSiembra::where('id_siembra', $campo['id_siembra'])->where('id_especie', $campo['id_especie'])->first();
			$biomasa = 0;

			if ($request->tipo_registro == 0) {

				$exs->cant_actual = $exs->cant_actual - $campo['mortalidad'];

				if ($campo['peso_ganado'] > $exs->peso_actual) {
					$exs->peso_actual = $campo['peso_ganado'];
				}
				$exs->save();

				if ($campo['peso_ganado'] == '' && $campo['mortalidad'] != '') {

					$biomasa = ($campo['mortalidad'] * $exs->peso_actual) / 1000;
					$registro = Registro::create([
						'id_especie' => $campo['id_especie'],
						'id_siembra' => $campo['id_siembra'],
						'fecha_registro' => $request['fecha_registro'],
						'tipo_registro' => $request['tipo_registro'],
						'peso_ganado' => $exs['peso_actual'],
						'mortalidad' => $campo['mortalidad'],
						'biomasa' => $biomasa
					]);
				} elseif ($campo['peso_ganado'] != '' || $campo['mortalidad'] != '') {
					$biomasa = ($campo['mortalidad'] * $campo['peso_ganado']) / 1000;
					$registro = Registro::create([
						'id_especie' => $campo['id_especie'],
						'id_siembra' => $campo['id_siembra'],
						'fecha_registro' => $request['fecha_registro'],
						'tipo_registro' => $request['tipo_registro'],
						'peso_ganado' => $campo['peso_ganado'],
						'mortalidad' => $campo['mortalidad'],
						// 'cantidad' => $exs['cant_actual'],
						'biomasa' => $biomasa
					]);
				} elseif ($campo['peso_ganado'] != '' || $campo['mortalidad'] == '') {
					// $biomasa = ($campo['mortalidad'] * $campo['peso_ganado']) / 1000;
					$registro = Registro::create([
						'id_especie' => $campo['id_especie'],
						'id_siembra' => $campo['id_siembra'],
						'fecha_registro' => $request['fecha_registro'],
						'tipo_registro' => $request['tipo_registro'],
						'peso_ganado' => $campo['peso_ganado'],
						'mortalidad' => $campo['mortalidad'],
						'biomasa' => $biomasa
					]);
				}
			}
			if ($campo['biomasa'] != '') {
				if ($request->tipo_registro == 1) {
					$registro = Registro::create([
						'id_especie' => $campo['id_especie'],
						'id_siembra' => $campo['id_siembra'],
						'fecha_registro' => $request['fecha_registro'],
						'tipo_registro' => $request['tipo_registro'],
						'biomasa' => $campo['biomasa'],
						'cantidad' => (($campo['biomasa'] * 1000) / $exs->peso_actual),
						'peso_ganado' => $campo['peso_actual'],
					]);

					$exs->cant_actual = $exs->cant_actual - $registro->cantidad;
					$exs->save();
				}
			}
			if ($request->tipo_registro == 2) {
				$biomasa = ($campo['mortalidad'] * $exs->peso_inicial) / 1000;

				if (($campo['mortalidad'] > 0) && ($campo['mortalidad'] < $exs->cant_actual)) {
					$exs->cant_actual = $exs->cant_actual - $campo['mortalidad'];
				}
				$exs->save();
				if ($campo['mortalidad']) {
					$registro = Registro::create([
						'id_especie' => $campo['id_especie'],
						'id_siembra' => $campo['id_siembra'],
						'fecha_registro' => $request['fecha_registro'],
						'tipo_registro' => $request['tipo_registro'],
						'mortalidad' => $campo['mortalidad'],
						'peso_ganado' => $exs['peso_inicial'],
						'biomasa' => $biomasa
					]);
				}
			}
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		Registro::destroy($id);

		$oldData = $this->ultimoRegistroEspecieSiembra($request['campos']['id_siembra'], $request['campos']['id_especie']);

		$exs = EspecieSiembra::where('id_siembra', $request['campos']['id_siembra'])->where('id_especie', $request['campos']['id_especie'])->first();
		if ($request['campos']['mortalidad'] > 0) {
			$exs->cant_actual = $exs->cant_actual + $request['campos']['mortalidad'];
		}

		$exs->peso_actual = $oldData['peso_actual'];
		$exs->cant_actual = $oldData['cantidad_actual'];

		$exs->save();
	}

	public function ultimoRegistroEspecieSiembra($id_siembra, $id_especie)
	{

		$exs = EspecieSiembra::where('id_siembra', $id_siembra)->where('id_especie', $id_especie)->first();

		$peso = Registro::select()
			->where('registros.id_siembra', '=', $id_siembra)
			->where('registros.id_especie', '=', $id_especie)
			->where('registros.peso_ganado', '!=', '')
			->orderBy('registros.id', 'desc')
			->first();

		if ($peso != '' || $peso != NULL) {
			$peso_actual = $peso->peso_ganado;
		} else {
			$peso_actual = $exs->peso_inicial;
		}

		$cantidad = Registro::select()
			->where('registros.id_siembra', '=', $id_siembra)
			->where('registros.id_especie', '=', $id_especie)
			->where('registros.cantidad', '!=', '')
			->orderBy('registros.id', 'desc')
			->first();

		if ($cantidad != '' || $cantidad != NULL) {
			$cantidad_actual = $cantidad->cantidad;
		} else {
			$cantidad_actual = $exs->cantidad;
		}

		return [
			'peso_actual' => $peso_actual,
			'cantidad_actual' => $cantidad_actual
		];
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id, Request $request)
	{
		//
		$registro = Registro::destroy($id);
	}

}
