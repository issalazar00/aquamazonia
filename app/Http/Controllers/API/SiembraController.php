<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\EspecieSiembra;
use App\Especie;
use App\Siembra;
use App\Contenedor;
use App\Registro;

class SiembraController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function index(Request $request)
	{
		//Otros datos
		$contenedor_id = (int) $request['contenedor_id'] ?? -1;
		$sign_contenedor = ($contenedor_id <= 0) ? '<>' : '=';
		$siembra_id = (int) $request['id_siembra'] ?: -1;
		$sign_siembra_id = ($siembra_id <= 0) ? '<>' : '=';

		$estado_siembra = $request['estado_siembra'] ?? -1;
		$sign_estado_siembra = ($estado_siembra == "-1") ? '<>' : '=';

		$phase = $request->phase;
		$type = $request->type;
		$nro_results = $request->nro_results ?? 15;

		$siembras = Siembra::select('siembras.id as id', 'nombre_siembra', 'id_contenedor', 'contenedor', 'fecha_inicio', 'ini_descanso', 'fin_descanso', 'siembras.estado as estado', 'fecha_alimento', 'phase_id', 'tipo')
			->join('contenedores', 'siembras.id_contenedor', 'contenedores.id')
			->where('siembras.id', $sign_siembra_id, $siembra_id)
			->where('siembras.estado', $sign_estado_siembra, $estado_siembra)
			->where(function ($query) use ($phase) {
				if (!is_null($phase) && $phase != '' && $phase != 'all') {
					$query->where('phase_id', "LIKE", "%$phase%");
				}
			})
			->where(function ($query) use ($type) {
				if (!is_null($type) && $type != '' && $type != 'all') {
					$query->where('tipo', "LIKE", "%$type%");
				}
			})

			->where('id_contenedor', $sign_contenedor, $contenedor_id)
			->with('peces.especie')
			->orderBy('siembras.id', 'asc')->paginate($nro_results);

		$siembras->map(function ($siembra) {
			$siembra->peces->map(function ($pez) {

				$especies_siembra = new EspeciesSiembraController();
				$mortalidad = $especies_siembra->cantidadEspecieSiembra($pez->id_siembra, $pez->id_especie)->mortalidad ?? 0;
				$salida = $especies_siembra->cantidadEspecieSiembra($pez->id_siembra, $pez->id_especie)->cantidad ?? 0;
				$pez->cant_actual = $pez->cantidad - $salida - $mortalidad;
				$pez->mortalidad =  $mortalidad;
				$pez->salida =  $salida;
				return $pez;
			});
			return $siembra;
		});

		$fecha_actual = date('Y-m-d');

		return ["siembra" => $siembras, 'fecha_actual' => $fecha_actual];
	}

	public function campos(Request $request)
	{
		$peces = EspecieSiembra::select('especies_siembra.id as id', 'id_siembra', 'id_especie', 'lote', 'cantidad', 'peso_inicial', 'cant_actual',  'peso_actual', 'especies.especie as especie',)
			->join('especies', 'especies_siembra.id_especie', 'especies.id')
			->orderBy('especie', 'asc')
			->where('id_siembra', $request->siembra_id)
			->get()->toArray();

		$campos = array();
		foreach ($peces as $p) {

			$especies_siembra = new EspeciesSiembraController();
			$mortalidad = $especies_siembra->cantidadEspecieSiembra($p['id_siembra'], $p['id_especie'])->mortalidad ?? 0;
			$salida = $especies_siembra->cantidadEspecieSiembra($p['id_siembra'], $p['id_especie'])->cantidad ?? 0;
			$cantidad_actual_pez = $p['cantidad'] - $salida - $mortalidad;

			$campos[$p['id_siembra']][$p['id']] = array(
				"id_especie" => $p['id_especie'],
				"id_siembra" => $p['id_siembra'],
				"peso_ganado" => '',
				"mortalidad" => '',
				"biomasa" => '',
				"cantidad" => '',
				'cant_actual' => $cantidad_actual_pez,
				'peso_actual' => $p['peso_actual']
			);
		}

		return $campos;
	}

	public function listadoLotes()
	{
		return EspecieSiembra::select('lote')->orderBy('lote', 'asc')->distinct()->get();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$id_contenedor = $request->siembra['id_contenedor'];

		$siembra = new Siembra();
		$siembra->id_contenedor = $request->siembra['id_contenedor'];
		$siembra->nombre_siembra = $request->siembra['nombre_siembra'];
		$siembra->fecha_inicio = $request->siembra['fecha_inicio'];
		$siembra->tipo = $request->siembra['tipo'];
		$siembra->estado = 1;
		$siembra->save();

		$contenedor = Contenedor::findOrFail($id_contenedor);
		$contenedor->update([$contenedor->estado = 2]);

		foreach ($request->especies as $especie) {
			$especieSiembra = new EspecieSiembra();
			$especieSiembra->id_siembra = $siembra->id;
			$especieSiembra->id_especie = $especie['id_especie'];
			$especieSiembra->cantidad =  $especie['cantidad'];
			$especieSiembra->lote =  $especie['lote'];
			$especieSiembra->peso_inicial = $especie['peso_inicial'];
			$especieSiembra->cant_actual =  $especie['cantidad'];
			$especieSiembra->peso_actual = $especie['peso_inicial'];
			$especieSiembra->save();


			Registro::create([
				'id_especie' => $especie['id_especie'],
				'id_siembra' => $siembra->id,
				'fecha_registro' => $request->siembra['fecha_inicio'],
				'tipo_registro' => 3,
				'peso_ganado' => $especie['peso_inicial'],
				// 'cantidad' =>$especie['cantidad']
			]);
		}
	}
	// Añadir especies a la siembra
	public function anadirEspeciesxSiembra(Request $request)
	{
		foreach ($request['especies'] as $especie) {
			if (!isset($especie['es_edita'])) {
				$especieSiembra = new EspecieSiembra();
				$especieSiembra->id_siembra = $request->siembra['siembra_id'];
				$especieSiembra->id_especie = $especie['id_especie'];
				$especieSiembra->cantidad =  $especie['cantidad'];
				$especieSiembra->lote =  $especie['lote'];
				$especieSiembra->peso_inicial = $especie['peso_inicial'];
				$especieSiembra->cant_actual =  $especie['cantidad'];;
				$especieSiembra->peso_actual = $especie['peso_inicial'];
				$especieSiembra->save();

				$registro = Registro::create([
					'id_especie' => $especie['id_especie'],
					'id_siembra' => $request->siembra['siembra_id'],
					'fecha_registro' => $request->siembra['fecha_inicio'],
					'tipo_registro' => 3,
					'peso_ganado' => $especie['peso_inicial'],
					// 'cantidad' =>$especie['cantidad']
				]);
			}
		}
	}



	/**
	 * Display the specified unidad.
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
		$especieSiembras = EspecieSiembra::findOrFail($id);
		$especieSiembras->update($request->all());

		$registro = Registro::where('id_siembra', $especieSiembras->id_siembra)->where('id_especie', $especieSiembras->id_especie)->where('tipo_registro', 3)->first();
		$registro->peso_ganado = $request->peso_inicial;
		$registro->save();

		return $especieSiembras;
	}

	public function actualizarEstado(Request $request, $id)
	{
		$siembra = Siembra::findOrFail($id);
		$siembra->ini_descanso = $request['ini_descanso'];
		if (isset($request['fin_descanso'])) {
			$siembra->fin_descanso = $request['fin_descanso'];
		}
		$siembra->estado = 0;
		$siembra->save();

		return $request;
	}

	public function changeStatus(Request $request, $id)
	{
		$siembra = Siembra::findOrFail($id);
		$siembra->fin_descanso = date('Y-m-d');
		$siembra->estado = 1;
		$siembra->save();

		return $request;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		Siembra::destroy($id);
		EspecieSiembra::where('id_siembra', $id)->delete();
		Registro::where('id_siembra', $id)->delete();

		return 'eliminado';
	}

	public function filtroSiembras(Request $request)
	{
		$c1 = "siembras.id";
		$op1 = '!=';
		$c2 = '-1';
		$c3 = "siembras.id";
		$op2 = '!=';
		$c4 = '-1';
		$c5 = "siembras.id";
		$op3 = '!=';
		$c6 = '-1';
		$c7 = "siembras.id";
		$op4 = '!=';
		$c8 = '-1';
		$c9 = "siembras.id";
		$op5 = '!=';
		$c10 = '-1';

		if ($request['f_siembra'] != '-1') {
			$c1 = "siembras.id";
			$op1 = '=';
			$c2 = $request['f_siembra'];
		}
		if ($request['f_especie'] != '-1') {
			$c3 = "especies.id";
			$op2 = '=';
			$c4 = $request['f_especie'];
		}
		if ($request['f_lote'] != '-1') {
			$c5 = "lote";
			$op3 = '=';
			$c6 = $request['f_lote'];
		}
		if ($request['f_inicio_d'] != '-1') {
			$c7 = "fecha_inicio";
			$op4 = '>=';
			$c8 = $request['f_inicio_d'];
		}
		if ($request['f_inicio_h'] != '-1') {
			$c9 = "fecha_inicio";
			$op5 = '<=';
			$c10 = $request['f_inicio_h'];
		}

		$filtrarSiembras = Siembra::select('siembras.id as id', 'nombre_siembra', 'id_contenedor', 'contenedor', 'fecha_inicio', 'ini_descanso', 'fin_descanso', 'siembras.estado as estado', 'lote', 'especies.id', 'especie', 'cantidad', 'peso_inicial', 'cant_actual', 'peso_actual', 'fecha_alimento')
			->join('contenedores', 'siembras.id_contenedor', 'contenedores.id')
			->join('especies_siembra', 'siembras.id', 'especies_siembra.id_siembra')
			->join('especies', 'especies_siembra.id_especie', 'especies.id')
			->where($c1, $op1, $c2)
			->where($c3, $op2, $c4)
			->where($c5, $op3, $c6)
			->where($c7, $op4, $c8)
			->where($c9, $op5, $c10)
			->orderBy('siembras.id', 'desc')
			->get();
		return ['filtrarSiembras' => $filtrarSiembras];
	}
	public function traerSiembras()
	{
		$filtrarSiembras = Siembra::select('siembras.id as id', 'nombre_siembra', 'id_contenedor', 'contenedor', 'fecha_inicio', 'ini_descanso', 'fin_descanso', 'siembras.estado as estado', 'lote', 'especies.id', 'especie', 'cantidad', 'peso_inicial', 'cant_actual', 'peso_actual', 'fecha_alimento')
			->join('contenedores', 'siembras.id_contenedor', 'contenedores.id')
			->join('especies_siembra', 'siembras.id', 'especies_siembra.id_siembra')
			->join('especies', 'especies_siembra.id_especie', 'especies.id')
			->orderBy('siembras.id', 'desc')
			->get();

		return ['filtrarSiembras' => $filtrarSiembras];
	}

	public function getEspeciesSiembra(Request $request, $id)
	{
		$espxsiembra = EspecieSiembra::select('especies_siembra.id as id', 'cantidad', 'id_especie', 'lote', 'peso_inicial', 'cant_actual')
			->join('especies', 'especies_siembra.id_especie', 'especies.id')
			->where('id_siembra', $id)
			->orderBy('especies.especie')
			->get();

		$aux_id_es = array();
		$aux_es = array();
		foreach ($espxsiembra as $axs) {
			$aux_id_es[] = $axs->id_especie;
			$aux_es[] = array('id' => $axs->id, 'cantidad' => $axs->cantidad, 'cant_actual' => $axs->cant_actual, 'id_especie' => $axs->id_especie, 'lote' => $axs->lote, 'peso_inicial' => $axs->peso_inicial, 'es_edita' => '1');
		}
		if (count($aux_id_es) > 0) {
			$especies = Especie::whereNotIn('id', $aux_id_es)->orderBy('especie')->get();
		} else {
			$especies = Especie::orderBy('especie')->get();
		}
		return ['espxsiembra' => $aux_es, 'especies' => $especies];
	}


	public function listadoSiembras()
	{
		$listado_siembras = Siembra::select()
			->get();

		return $listado_siembras;
	}
}
