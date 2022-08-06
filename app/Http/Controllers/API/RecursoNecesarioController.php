<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RecursoNecesario;
use App\RecursoSiembra;

use App\Recursos;
use App\Siembra;
use Illuminate\Support\Facades\DB;


class RecursoNecesarioController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		//
		$minutos_hombre = Recursos::select()->where('recurso', 'Minutos hombre')->orWhere('recurso', 'Minuto hombre')->orWhere('recurso', 'Minutos')->first();

		$recursosNecesarios = RecursoNecesario::orderBy('fecha_ra', 'desc')
			->join('recursos_siembras', 'recursos_necesarios.id', 'recursos_siembras.id_registro')
			->leftJoin('recursos', 'recursos_necesarios.id_recurso', 'recursos.id')
			->join('siembras', 'recursos_siembras.id_siembra', 'siembras.id')
			->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
			->where('tipo_actividad', '!=', '1')
			->where('estado', 1)
			->select('recursos_necesarios.id as id', 'cantidad_recurso', 'cant_manana', 'cant_tarde', 'id_recurso', 'id_alimento', 'recursos_siembras.id_siembra', 'actividad', 'conv_alimenticia', 'costo', 'recursos_necesarios.detalles', 'tipo_actividad', 'recurso', 'nombre_siembra', 'minutos_hombre', 'fecha_ra')
			->paginate(20);

		$promedioRecursos = array();
		$summh = 0;
		$sumtmh = 0;
		$sumcr = 0;
		$sumc = 0;
		$sumctr = 0;

		if (count($recursosNecesarios) > 0) {
			for ($i = 0; $i < count($recursosNecesarios); $i++) {
				$recursosNecesarios[$i]->costo_total_recurso = $recursosNecesarios[$i]->cantidad_recurso * $recursosNecesarios[$i]->costo;
				$recursosNecesarios[$i]->total_minutos_hombre = $recursosNecesarios[$i]->minutos_hombre * $minutos_hombre->costo;
				$summh += $recursosNecesarios[$i]->minutos_hombre;
				$sumtmh += $recursosNecesarios[$i]->total_minutos_hombre;
				$sumcr += $recursosNecesarios[$i]->cantidad_recurso;
				$sumc += $recursosNecesarios[$i]->costo;
				$sumctr += $recursosNecesarios[$i]->costo_total_recurso;
			}
			$promedioRecursos['tmh'] = $summh;
			$promedioRecursos['ttmh'] = $sumtmh;
			$promedioRecursos['tcr'] = $sumcr;
			$promedioRecursos['tc'] = $sumc;
			$promedioRecursos['ctr'] = $sumctr;
		}

		return [
			'recursosNecesarios' => $recursosNecesarios,
			'promedioRecursos' => $promedioRecursos,
			'pagination' => [
				'total'        => $recursosNecesarios->total(),
				'current_page' => $recursosNecesarios->currentPage(),
				'per_page'     => $recursosNecesarios->perPage(),
				'last_page'    => $recursosNecesarios->lastPage(),
				'from'         => $recursosNecesarios->firstItem(),
				'to'           => $recursosNecesarios->lastItem(),
			]
		];
	}
	public function  alimentacion(Request $request)
	{
		$minutos_hombre = Recursos::select()->where('recurso', 'Minutos hombre')->orWhere('recurso', 'Minuto hombre')->orWhere('recurso', 'Minutos')->first();


		$tipo_actividad = "recursos_necesarios.id";
		$filtro_tipo_actividad = "!=";
		$c2 = "-1";

		$filtroFechaRegistroAlimentoDesde = "recursos_necesarios.id";
		$signoFechaRegistroAlimentoDesde = "!=";
		$valorFechaRegistroAlimentoDesde = "-3";

		$filtroFechaRegistroAlimentoHasta = "recursos_necesarios.id";
		$signoFechaRegistroAlimentoHasta = "!=";
		$valorFechaRegistroAlimentohasta = "-1";

		$filtroIdAlimento = "recursos_necesarios.id_alimento";
		$signoIdAlimento = "!=";
		$valorIdAlimento = "-1";

		$filtroIdSiembra = 'recursos_necesarios.id';
		$signoIdSiembra = '!=';
		$valorIdSiembra = '-1';

		if ($request['tipo_actividad'] != '-1') {
			$tipo_actividad = "tipo_actividad";
			$filtro_tipo_actividad = '=';
			$id_tipo_actividad = $request['tipo_actividad'];
		} elseif ($request['tipo_actividad'] == '-1') {
			$tipo_actividad = "tipo_actividad";
			$filtro_tipo_actividad = '!=';
			$id_tipo_actividad = '-1';
		}

		if ($request['fecha_ra1'] != '-3') {
			$filtroFechaRegistroAlimentoDesde = "fecha_ra";
			$signoFechaRegistroAlimentoDesde = '>=';
			$valorFechaRegistroAlimentoDesde = $request['fecha_ra1'];
		}
		if ($request['fecha_ra2'] != '-1') {
			$filtroFechaRegistroAlimentoHasta = "fecha_ra";
			$signoFechaRegistroAlimentoHasta = '<=';
			$valorFechaRegistroAlimentohasta = $request['fecha_ra2'];
		}

		if (isset($request['alimento_s']) && $request['alimento_s'] != '-1') {
			$filtroIdAlimento = "id_alimento";
			$signoIdAlimento = '=';
			$valorIdAlimento = $request['alimento_s'];
		}

		if ($request['f_siembra'] != '-1') {
			$filtroIdSiembra = "recursos_necesarios.siembra_id";
			$signoIdSiembra = '=';
			$valorIdSiembra = $request['f_siembra'];
		}

		$recursosNecesarios = RecursoNecesario::orderBy('fecha_ra', 'desc')
			->select('*', 'recursos_necesarios.id as id')
			->join('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id')
			->join('siembras', 'recursos_necesarios.siembra_id', 'siembras.id')
			->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
			->where($filtroIdSiembra, $signoIdSiembra, $valorIdSiembra)
			->where($filtroIdAlimento, $signoIdAlimento, $valorIdAlimento)
			->where('tipo_actividad', '=', '1');

		$recursosNecesarios = $request['see_all'] ? $recursosNecesarios->get() : $recursosNecesarios->paginate(20);
		$promedioRecursos = array();

		$counter = count($recursosNecesarios);


		for ($i = 0; $i < count($recursosNecesarios); $i++) {

			$recursosNecesarios[$i]->total_minutos_hombre = $recursosNecesarios[$i]->minutos_hombre * $minutos_hombre->costo;
			$recursosNecesarios[$i]->costo_total_alimento = ($recursosNecesarios[$i]->cant_tarde + $recursosNecesarios[$i]->cant_manana) * $recursosNecesarios[$i]->costo_kg;
			$recursosNecesarios[$i]->alimento_dia = $recursosNecesarios[$i]->cant_tarde + $recursosNecesarios[$i]->cant_manana;

			if ($recursosNecesarios[$i]->conv_alimenticia > 0) {
				$recursosNecesarios[$i]->incr_bio_acum_conver = $recursosNecesarios[$i]->alimento_dia / $recursosNecesarios[$i]->conv_alimenticia;
				$recursosNecesarios[$i]->conv_alimenticia = number_format($recursosNecesarios[$i]->conv_alimenticia, 2, ',', '');
			}
		}

		$copyRecursosNecesarios = $request['see_all'] ?  $recursosNecesarios->toArray() : $recursosNecesarios->toArray()['data'];

		$promedioRecursos['tmh'] = array_sum(array_column($copyRecursosNecesarios, 'minutos_hombre'));
		$promedioRecursos['ttmh'] = array_sum(array_column($copyRecursosNecesarios, 'total_minutos_hombre'));

		$promedioRecursos['tc'] = array_sum(array_column($copyRecursosNecesarios, 'costo'));
		$promedioRecursos['ctr'] = array_sum(array_column($copyRecursosNecesarios, 'costo_total_recurso'));
		$promedioRecursos['cman'] =  array_sum(array_column($copyRecursosNecesarios, 'cant_manana'));
		$promedioRecursos['ctar'] =  array_sum(array_column($copyRecursosNecesarios, 'cant_tarde'));
		$promedioRecursos['alid'] = array_sum(array_column($copyRecursosNecesarios, 'alimento_dia'));
		$promedioRecursos['coskg'] = array_sum(array_column($copyRecursosNecesarios, 'costo_kg'));
		$promedioRecursos['cta'] = array_sum(array_column($copyRecursosNecesarios, 'costo_total_alimento'));
		$promedioRecursos['icb'] = array_sum(array_column($copyRecursosNecesarios, 'incr_bio_acum_conver'));
		$promedioRecursos['tc'] = $promedioRecursos['tc'];
		$promedioRecursos['ctr'] = $promedioRecursos['ctr'];
		$promedioRecursos['coskg'] = $promedioRecursos['coskg'] / $counter;
		$promedioRecursos['cta'] = $promedioRecursos['cta'];

		if ($request['see_all']) {
			return [
				'recursosNecesarios' => $recursosNecesarios,
				'promedioRecursos' => $promedioRecursos

			];
		} else {
			return [
				'recursosNecesarios' => $recursosNecesarios,
				'promedioRecursos' => $promedioRecursos,
				'pagination' => [
					'total'        => $recursosNecesarios->total(),
					'current_page' => $recursosNecesarios->currentPage(),
					'per_page'     => $recursosNecesarios->perPage(),
					'last_page'    => $recursosNecesarios->lastPage(),
					'from'         => $recursosNecesarios->firstItem(),
					'to'           => $recursosNecesarios->lastItem(),
				],
			];
		}
	}
	
	public function siembraxAlimentacion($id)
	{
		//
		$minutos_hombre = Recursos::select()->where('recurso', 'Minutos hombre')->orWhere('recurso', 'Minuto hombre')->orWhere('recurso', 'Minutos')->first();
		$recursosNecesarios = RecursoNecesario::orderBy('fecha_ra', 'desc')
			->join('recursos_siembras', 'recursos_necesarios.id', 'recursos_siembras.id_registro')
			->join('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id')
			->join('siembras', 'recursos_siembras.id_siembra', 'siembras.id')
			->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
			->where('id_siembra', '=', $id)
			->where('tipo_actividad', '=', '1')
			->get();
		if (count($recursosNecesarios) > 0) {
			for ($i = 0; $i < count($recursosNecesarios); $i++) {
				$recursosNecesarios[$i]->costo_total_alimento = ($recursosNecesarios[$i]->cant_tarde + $recursosNecesarios[$i]->cant_manana) * $recursosNecesarios[$i]->costo_kg;
				$recursosNecesarios[$i]->total_minutos_hombre = $recursosNecesarios[$i]->minutos_hombre * $minutos_hombre->costo;
				$recursosNecesarios[$i]->alimento_dia = $recursosNecesarios[$i]->cant_tarde + $recursosNecesarios[$i]->cant_manana;
			}
		}

		return ['recursosNecesarios' => $recursosNecesarios];
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
		$c_alim = RecursoNecesario::select()->orderBy('id', 'desc')->first();

		$recursoNecesario = new RecursoNecesario();
		$recursoNecesario->id_recurso = $request['id_recurso'];
		$recursoNecesario->id_alimento =  $request['id_alimento'];
		$recursoNecesario->tipo_actividad = $request['tipo_actividad'];
		$recursoNecesario->fecha_ra = $request['fecha_ra'];
		$recursoNecesario->minutos_hombre = $request['minutos_hombre'];
		$recursoNecesario->horas_hombre = ($request['minutos_hombre'] / 60);
		$recursoNecesario->cantidad_recurso = $request['cantidad_recurso'];
		$recursoNecesario->cant_manana = $request['cant_manana'];
		$recursoNecesario->cant_tarde = $request['cant_tarde'];
		if ($request['conv_alimenticia'] == '') {
			$recursoNecesario->conv_alimenticia = $c_alim->conv_alimenticia;
		} else {
			$recursoNecesario->conv_alimenticia = $request['conv_alimenticia'];
		}
		$recursoNecesario->detalles = $request['detalles'];

		if (!is_array($request['id_siembra'])) {

			$recursoNecesario->siembra_id = $request['id_siembra'];
			$recursoNecesario->save();

			$siembras = Siembra::findOrFail($request['id_siembra']);
			$siembras->fecha_alimento = $request['fecha_ra'];
			$siembras->save();
		} else {
			foreach ($request->id_siembra as $siembra) {

				$recursoNecesario->siembra_id = $siembra;
				$recursoNecesario->save();

				$siembras = Siembra::findOrFail($siembra);
				$siembras->fecha_alimento = $request['fecha_ra'];
				$siembras->save();
			}
		}

		return ($request);
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
		//
		$minutos =  $request['minutos_hombre'] / 60;
		$recursoNecesario = RecursoNecesario::findOrFail($id);
		$recursoNecesario->update([
			'fecha_ra' => $request['fecha_ra'],
			'cant_manana' => $request['cant_manana'],
			'cant_tarde' => $request['cant_tarde'],
			'id_alimento' => $request['id_alimento'],
			'id_recurso' => $request['id_recurso'],
			'detalles' => $request['detalles'],
			'minutos_hombre' => $request['minutos_hombre'],
			'horas_hombre' => $minutos,
			'cantidad_recurso' => $request['cantidad_recurso'],
		]);

		return ['recursoNecesario' => $recursoNecesario];
		// return 'ok';
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
		RecursoNecesario::destroy($id);
		$rxs = RecursoSiembra::where('id_registro', $id)->delete();
		return 'eliminado';
	}
	public function searchResults(Request $request)
	{

		$minutos_hombre = Recursos::select()->where('recurso', 'Minutos hombre')->orWhere('recurso', 'Minuto hombre')->orWhere('recurso', 'Minutos')->first();

		$tipo_actividad = "recursos_necesarios.id";
		$filtro_tipo_actividad = "!=";
		$c2 = "-1";
		$c3 = "recursos_necesarios.id";
		$op2 = "!=";
		$c4 = "-3";
		$c5 = "recursos_necesarios.id";
		$op3 = "!=";
		$c6 = "-1";
		$c7 = "recursos_necesarios.id";
		$op4 = "!=";
		$c8 = "-1";
		$c9 = "recursos_necesarios.id";
		$op5 = "!=";
		$c10 = "-1";
		$c11 = 'recursos_necesarios.id';
		$op6 = '!=';
		$c12 = '-1';
		$filtroIdSiembra = 'recursos_necesarios.id';
		$signoIdSiembra = '!=';
		$c14 = '-1';

		if ($request['tipo_actividad'] != '-1') {
			$tipo_actividad = "tipo_actividad";
			$filtro_tipo_actividad = '=';
			$id_tipo_actividad = $request['tipo_actividad'];
		} elseif ($request['tipo_actividad'] == '-1') {
			$tipo_actividad = "tipo_actividad";
			$filtro_tipo_actividad = '!=';
			$id_tipo_actividad = '-1';
		}

		if ($request['fecha_ra1'] != '-3') {
			$c3 = "fecha_ra";
			$op2 = '>=';
			$c4 = $request['fecha_ra1'];
		}
		if ($request['fecha_ra2'] != '-1') {
			$c5 = "fecha_ra";
			$op3 = '<=';
			$c6 = $request['fecha_ra2'];
		}
		if ($request['f_siembra'] != '-1') {
			$c7 = "siembras.id";
			$op4 = '=';
			$c8 = $request['f_siembra'];
		}
		if (isset($request['alimento_s']) && $request['alimento_s'] != '-1') {
			$c9 = "id_alimento";
			$op5 = '=';
			$c10 = $request['alimento_s'];
		}
		if (isset($request['recurso_s']) &&  ($request['recurso_s'] != '-1')) {
			$c11 = "id_recurso";
			$op6 = '=';
			$c12 = $request['recurso_s'];
		}
		if ($request['f_siembra'] != '-1') {
			$filtroIdSiembra = "recursos_necesarios.siembra_id";
			$signoIdSiembra = '=';
			$valorIdSiembra = $request['f_siembra'];
		}

		$recursosNecesarios = RecursoNecesario::orderBy('fecha_ra', 'desc')
			->select('*', 'recursos_necesarios.id as id');

		if ($request['tipo_actividad'] == 1) {
			$recursosNecesarios = 	$recursosNecesarios->join('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id');
		} else {
			$recursosNecesarios = 	$recursosNecesarios->join('recursos', 'recursos_necesarios.id_recurso', 'recursos.id');
		}

		$recursosNecesarios = 	$recursosNecesarios->join('siembras', 'recursos_necesarios.siembra_id', 'siembras.id')
			->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
			->where($tipo_actividad, $filtro_tipo_actividad, $id_tipo_actividad)
			->where($c3, $op2, $c4)
			->where($c5, $op3, $c6)
			// ->where($c7, $op4, $c8)
			->where($c9, $op5, $c10)
			->where($c11, $op6, $c12)
			->where($filtroIdSiembra, $signoIdSiembra, $valorIdSiembra);

		if ($request['see_all']) {
			$recursosNecesarios = $recursosNecesarios->get();
		} else {
			$recursosNecesarios = $recursosNecesarios->paginate(20);
		}

		$promedioRecursos = array();

		$counter = count($recursosNecesarios);

		if (count($recursosNecesarios) > 0) {
			for ($i = 0; $i < count($recursosNecesarios); $i++) {

				$recursosNecesarios[$i]->costo_total_recurso = $recursosNecesarios[$i]->cantidad_recurso * $recursosNecesarios[$i]->costo;
				$recursosNecesarios[$i]->total_minutos_hombre = $recursosNecesarios[$i]->minutos_hombre * $minutos_hombre->costo;

				$recursosNecesarios[$i]->costo_total_alimento = ($recursosNecesarios[$i]->cant_tarde + $recursosNecesarios[$i]->cant_manana) * $recursosNecesarios[$i]->costo_kg;
				$recursosNecesarios[$i]->alimento_dia = $recursosNecesarios[$i]->cant_tarde + $recursosNecesarios[$i]->cant_manana;
				if ($recursosNecesarios[$i]->conv_alimenticia > 0) {
					$recursosNecesarios[$i]->incr_bio_acum_conver = $recursosNecesarios[$i]->alimento_dia / $recursosNecesarios[$i]->conv_alimenticia;
					$recursosNecesarios[$i]->conv_alimenticia = number_format($recursosNecesarios[$i]->conv_alimenticia, 2, ',', '');
				}
			}

			$copyRecursosNecesarios = $recursosNecesarios->toArray();

			// $totalizadoEspeciesSiembras['peso_inicial'] = array_sum(array_column($existencias, 'peso_inicial'));
			$promedioRecursos['tmh'] = array_sum(array_column($copyRecursosNecesarios, 'minutos_hombre'));
			$promedioRecursos['ttmh'] = array_sum(array_column($copyRecursosNecesarios, 'total_minutos_hombre'));
			$promedioRecursos['tcr'] = array_sum(array_column($copyRecursosNecesarios, 'cantidad_recurso'));
			$promedioRecursos['tc'] = array_sum(array_column($copyRecursosNecesarios, 'costo'));
			$promedioRecursos['ctr'] = array_sum(array_column($copyRecursosNecesarios, 'costo_total_recurso'));
			$promedioRecursos['cman'] =  array_sum(array_column($copyRecursosNecesarios, 'cant_manana'));
			$promedioRecursos['ctar'] =  array_sum(array_column($copyRecursosNecesarios, 'cant_tarde'));
			$promedioRecursos['alid'] = array_sum(array_column($copyRecursosNecesarios, 'alimento_dia'));
			$promedioRecursos['coskg'] = array_sum(array_column($copyRecursosNecesarios, 'costo_kg'));
			$promedioRecursos['cta'] = array_sum(array_column($copyRecursosNecesarios, 'costo_total_alimento'));
			$promedioRecursos['icb'] = array_sum(array_column($copyRecursosNecesarios, 'incr_bio_acum_conver'));
			$promedioRecursos['tc'] = $promedioRecursos['tc'];
			$promedioRecursos['ctr'] = $promedioRecursos['ctr'];
			$promedioRecursos['coskg'] = $promedioRecursos['coskg'] / $counter;
			$promedioRecursos['cta'] = $promedioRecursos['cta'];
		}


		if ($request['see_all']) {
			return [
				'recursosNecesarios' => $recursosNecesarios,
				'promedioRecursos' => $promedioRecursos

			];
		} else {
			return [
				'recursosNecesarios' => $recursosNecesarios,
				'promedioRecursos' => $promedioRecursos,
				'pagination' => [
					'total'        => $recursosNecesarios->total(),
					'current_page' => $recursosNecesarios->currentPage(),
					'per_page'     => $recursosNecesarios->perPage(),
					'last_page'    => $recursosNecesarios->lastPage(),
					'from'         => $recursosNecesarios->firstItem(),
					'to'           => $recursosNecesarios->lastItem(),
				],
			];
		}
	}


	public function recursosNecesariosPorSiembra($siembra_id)
	{
		$recursos_necesarios_siembra = RecursoNecesario::select(
			DB::raw("SUM(cantidad_recurso) AS cantidad_recurso"),
			DB::raw("SUM(minutos_hombre) AS minutos_hombre"),
			DB::raw("SUM(horas_hombre) AS horas_hombre"),
			DB::raw("SUM(cantidad_recurso*costo) AS costo_total_recurso"),
			DB::raw("SUM(cant_manana) AS cant_manana"),
			DB::raw("SUM(cant_tarde) AS cant_tarde"),
			DB::raw("SUM((cant_manana + cant_tarde) / conv_alimenticia) AS incr_bio_acum_conver"),
			DB::raw("SUM(cant_manana * costo_kg) AS costo_manana"),
			DB::raw("SUM(cant_tarde* costo_kg) AS costo_tarde"),
		)
			->leftJoin('recursos', 'recursos_necesarios.id_recurso', 'recursos.id')
			->leftJoin('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id')

			->groupBy('siembra_id')
			->where('siembra_id', $siembra_id)

			->get();
		return $recursos_necesarios_siembra;
	}

	public function alimentosPorSiembra($siembra_id)
	{
		$alimentos_siembra = RecursoNecesario::select(
			DB::raw("SUM(cant_manana) AS cant_manana"),
			DB::raw("SUM(cant_tarde) AS cant_tarde"),
			DB::raw("SUM((cant_manana + cant_tarde) / conv_alimenticia) AS incr_bio_acum_conver"),
			DB::raw("SUM((cant_manana + cant_tarde) / conv_alimenticia) AS incr_bio_acum_conver"),
			DB::raw("SUM(cant_manana * costo_kg) AS costo_manana"),
			DB::raw("SUM(cant_tarde* costo_kg) AS costo_tarde"),
		)
			->join('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id')
			->groupBy('siembra_id')
			->where('siembra_id', $siembra_id)
			->where('tipo_actividad', '1')
			->get();


		return $alimentos_siembra;
	}
}
