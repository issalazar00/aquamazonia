<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\EspeciesSiembraController;

use App\EspecieSiembra;
use App\Siembra;
use App\RecursoSiembra;
use App\RecursoNecesario;
use App\Recursos;
use App\Registro;
use App\Actividad;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{

		$c1 = 'tipo_actividad';
		$op1 = '!=';
		$c2 = '-1';
		$c3 = 'tipo_actividad';
		$op2 = '!=';
		$c4 = '-1';
		$c5 = 'tipo_actividad';
		$op3 = '!=';
		$c6 = '-1';
		$c7 = 'tipo_actividad';
		$op4 = '!=';
		$c8 = '-1';
		$c9 = 'tipo_actividad';
		$op5 = '!=';
		$c10 = '-1';
		$c11 = 'tipo_actividad';
		$op6 = '!=';
		$c12 = '-1';
		$simbra_id = 'tipo_actividad';
		$op7 = '!=';
		$filtro_siembra = '-1';
		$signCont = '!=';
		$idContenedor = '-1';

		if ($request['estado_s'] != '-1') {
			$c1 = "estado";
			$op1 = '=';
			$c2 = $request['estado_s'];
		}
		if ($request['actividad_s'] != '-1') {
			$c3 = "tipo_actividad";
			$op2 = '=';
			$c4 = $request['actividad_s'];
		}
		if ($request['alimento_s'] != '-1') {
			$c5 = "id_alimento";
			$op3 = '=';
			$c6 = $request['alimento_s'];
		}
		if ($request['recurso_s'] != '-1') {
			$c7 = "id_recurso";
			$op4 = '=';
			$c8 = $request['recurso_s'];
		}
		if ($request['fecha_ra1'] != '-1') {
			$c9 = "fecha_ra";
			$op5 = '>=';
			$c10 = $request['fecha_ra1'];
		}
		if ($request['fecha_ra2'] != '-1') {
			$c11 = "fecha_ra";
			$op6 = '<=';
			$c12 = $request['fecha_ra2'];
		}
		if ($request['f_siembra'] != '-1') {
			$simbra_id = "siembras.id";
			$op7 = '=';
			$filtro_siembra = $request['f_siembra'];
		}
		if ($request['f_contenedor'] != '-1') {
			$signCont = '=';
			$idContenedor = $request['f_contenedor'];
		}

		$recursosNecesarios = RecursoNecesario::orderBy('fecha_ra', 'desc')
			->leftJoin('recursos', 'recursos_necesarios.id_recurso', 'recursos.id')
			->leftJoin('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id')
			->join('siembras', 'recursos_necesarios.siembra_id', 'siembras.id')
			->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
			->select('recursos.id as idr', 'alimentos.id as ida', 'recursos_necesarios.id as id', 'actividad', 'minutos_hombre', 'id_recurso', 'id_alimento', 'fecha_ra', 'costo_minutos_hombre', 'cant_manana', 'cant_tarde', 'detalles', 'tipo_actividad', 'recurso', 'alimento', 'recursos.costo as costo_r', 'alimentos.costo_kg as costo_a', 'nombre_siembra', 'estado', 'cantidad_recurso', 'siembras.id_contenedor')
			->where($c1, $op1, $c2)
			->where($c3, $op2, $c4)
			->where($c5, $op3, $c6)
			->where($c7, $op4, $c8)
			->where($c9, $op5, $c10)
			->where($c11, $op6, $c12)
			->where($simbra_id, $op7, $filtro_siembra)
			->where('siembras.id_contenedor', $signCont, $idContenedor)
			->orderBy('recursos_necesarios.created_at', 'desc')
			->paginate(30);

		if (count($recursosNecesarios) > 0) {
			for ($i = 0; $i < count($recursosNecesarios); $i++) {

				$recursosNecesarios[$i]->costo_total_recurso = ($recursosNecesarios[$i]->cantidad_recurso * $recursosNecesarios[$i]->costo_r);
				$recursosNecesarios[$i]->costo_total_alimento = ($recursosNecesarios[$i]->cant_tarde + $recursosNecesarios[$i]->cant_manana) * $recursosNecesarios[$i]->costo_a;
				$recursosNecesarios[$i]->costo_minutosh = $recursosNecesarios[$i]->minutos_hombre * $recursosNecesarios[$i]->costo_minutos_hombre;
				$recursosNecesarios[$i]->costo_total_actividad = ($recursosNecesarios[$i]->costo_total_recurso + 	$recursosNecesarios[$i]->costo_total_alimento + 	$recursosNecesarios[$i]->costo_minutosh);
			}
		}

		return [
			'recursosNecesarios' => $recursosNecesarios,
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


	public function traerExistencias(Request $request)
	{
		$siembraId = "siembras.id";
		$signSiembraId = '!=';
		$valueSiembraId = '-1';

		$especieId = "siembras.id";
		$signEspecieId = '!=';
		$valueEspecieId = '-1';

		$fechaInicio = "siembras.id";
		$signFechaInicio = '!=';
		$valueFechaInicio = '-1';

		$fechaHasta = "siembras.id";
		$signFechaHasta = '!=';
		$valueFechaHasta = '-1';

		$signPesoDesde = '>=';
		$valuePesoDesde = '0';

		$signPesoHasta = '>=';
		$valuePesoHasta = '0';

		$signLote = '!=';
		$valueLote = '-1';
		$signEstado = '!=';
		$valueEstado = '-1';

		if ($request['f_siembra'] != '-1') {
			$siembraId = "id_siembra";
			$signSiembraId = '=';
			$valueSiembraId = $request['f_siembra'];
		}
		if ($request['f_especie'] != '-1') {
			$especieId = "id_especie";
			$signEspecieId = '=';
			$valueEspecieId = $request['f_especie'];
		}
		if ($request['f_inicio_d'] != '-1') {
			$fechaInicio = "fecha_inicio";
			$signFechaInicio = '>=';
			$valueFechaInicio = $request['f_inicio_d'];
		}

		if ($request['f_inicio_h'] != '-1') {
			$fechaHasta = "fecha_inicio";
			$signFechaHasta = '<=';
			$valueFechaHasta = $request['f_inicio_h'];
		}

		if ($request['f_peso_d'] != '-1') {
			$signPesoDesde = '>=';
			$valuePesoDesde = $request['f_peso_d'];
		}

		if ($request['f_peso_h'] != '-1') {
			$signPesoHasta = '<=';
			$valuePesoHasta = $request['f_peso_h'];
		}

		if ($request['f_lote'] != '-1') {
			$signLote = '=';
			$valueLote = $request['f_lote'];
		}
		if ($request['f_estado'] != '-1') {
			$signEstado = '=';
			$valueEstado = $request['f_estado'];
		}

		$phase = $request->phase;
		$type = $request->type;
		$nro_results = $request->nro_results ?? 15;

		$especies = EspecieSiembra::select(
			'cant_actual',
			'contenedor',
			'capacidad',
			'especies_siembra.cantidad as cantidad_inicial',
			'especies_siembra.id_especie as id_especie',
			'especies_siembra.id_siembra as id_siembra',
			'especies_siembra.lote as lote',
			'fecha_inicio',
			'nombre_siembra',
			'peso_inicial',
			'peso_actual',
			'siembras.phase_id'
		)
			->orderBy('nombre_siembra', 'desc')
			->orderBy('especies_siembra.id_especie')
			->join('siembras', 'especies_siembra.id_siembra', 'siembras.id')
			->join('contenedores', 'siembras.id_contenedor', 'contenedores.id')
			->where($siembraId, $signSiembraId, $valueSiembraId)
			->where($especieId, $signEspecieId, $valueEspecieId)
			->where($fechaInicio, $signFechaInicio, $valueFechaInicio)
			->where($fechaHasta, $signFechaHasta, $valueFechaHasta)
			->where('peso_actual', $signPesoDesde, $valuePesoDesde)
			->where('peso_actual', $signPesoHasta, $valuePesoHasta)
			->where('lote', $signLote, $valueLote)
			->where('siembras.estado', $signEstado, $valueEstado)
			->with(['especie:id,especie', 'siembra.phase'])
			->whereHas('siembra', function ($query) use ($phase, $type) {

				if (!is_null($phase) && $phase != '' && $phase != 'all') {
					$query->where('phase_id', "LIKE", "%$phase%");
				}
				if (!is_null($type) && $type != '' && $type != 'all') {
					$query->where('tipo', "LIKE", "%$type%");
				}
			})
			->paginate($nro_results);

		$int_tiempo = 0;

		$registros = Registro::select()->get();
		$especies_siembra = new EspeciesSiembraController;


		if (count($especies) > 0) {
			foreach ($especies as $especie) {
				$infoEspecieSiembra = $especies_siembra->cantidadEspecieSiembra($especie->id_siembra, $especie->id_especie);
				if ($infoEspecieSiembra) {
					$especie->mortalidad = $infoEspecieSiembra->mortalidad ?? 0;
					$especie->salida_animales =  $infoEspecieSiembra->cantidad + $especie->mortalidad;
					$especie->salida_animales_sin_mortalidad = $infoEspecieSiembra->cantidad;
					$especie->cantidad_actual = $especie->cantidad_inicial - $especie->salida_animales;
					$especie->mortalidad_kg = $infoEspecieSiembra->mortalidad_kg;
					$especie->mortalidad_porcentaje =  ($especie->mortalidad * 100) / $especie->cantidad_inicial;
				}
				$especie->biomasa_disponible = ((($especie->peso_actual) * ($especie->cantidad_actual)) / 1000);
				$especie->biomasa_inicial =  ((($especie->peso_inicial) * ($especie->cantidad_inicial)) / 1000);
				$especie->salida_biomasa = $especies_siembra->cantidadEspecieSiembraSinMortalidad($especie->id_siembra, $especie->id_especie)->biomasa ?? 0;
				// $especie->densidad_final = $especie->salida_animales_sin_mortalidad / $especie->capacidad;
				$especie->densidad_parcial = $especie->cantidad_actual / $especie->capacidad;
				// $especie->carga_final = $especie->salida_biomasa / $especie->capacidad;
				$especie->carga_parcial = $especie->biomasa_disponible / $especie->capacidad;
				$especie->peso_incremento = $especie->peso_actual -  $especie->peso_inicial;
				$especie->incremento_biomasa = (($especie->peso_incremento * $especie->cant_actual) / 1000);

				for ($j = 0; $j < count($registros); $j++) {

					if (count($registros) > 0) {
						if ($especie->id_siembra == $registros[$j]->id_siembra && $especie->id_especie == $registros[$j]->id_especie) {

							$int_tiempo = Registro::select('fecha_registro')
								->orderBy('fecha_registro', 'desc')
								->where('id_siembra', $especie->id_siembra)
								->where('id_especie', $especie->id_especie)
								->first();

							$date1 = new \DateTime($especie->fecha_inicio);
							$date2 = new \DateTime($int_tiempo->fecha_registro);
							$diff = $date1->diff($date2);

							$especie->fecha_registro = $int_tiempo->fecha_registro;
							$especie->intervalo_tiempo  = $diff->days;

							$especie->intervalo_tiempo_months  = $diff->days / 30;

							if ($especie->intervalo_tiempo > 0) {
								$especie->ganancia_peso_dia = $especie->peso_incremento / $especie->intervalo_tiempo;
							}
						}
					}
				}
			}
		}

		$totals = $this->getTotalExistences($especies);
		return ['existencias' => $especies, 'totalizadoEspeciesSiembras' => $totals];
	}

	public function traerExistenciasDetalle(Request $request)
	{

		$id_siembra = "siembras.id";
		$operador_id_siembra = '!=';
		$valorIdSiembra = '-1';

		$op2 = '!=';
		$c4 = '-1';
		$signCont = '!=';
		$valorIdContenedor = '-1';

		if ($request['f_siembra'] != '-1') {
			$id_siembra = "siembras.id";
			$operador_id_siembra = '=';
			$valorIdSiembra = $request['f_siembra'];
		}
		if ($request['f_contenedor'] != '-1') {
			$signCont = '=';
			$valorIdContenedor = $request['f_contenedor'];
		}
		if ($request['f_estado'] != '-1') {
			$op2 = '=';
			$c4 = $request['f_estado'];
		}

		$phase = $request->phase;
		$type = $request->type;
		$nro_results = $request->nro_results ?? 15;

		$siembras = Siembra::select(
			'siembras.id as id',
			'capacidad',
			'nombre_siembra',
			'id_contenedor',
			'fecha_inicio',
			'ini_descanso',
			'siembras.estado',
			'fin_descanso',
			'tipo',
			'phase_id'
		)
			->where($id_siembra, $operador_id_siembra, $valorIdSiembra)
			->where('id_contenedor', $signCont, $valorIdContenedor)
			->where('siembras.estado', $op2, $c4)
			->join('contenedores', 'siembras.id_contenedor', 'contenedores.id')
			->orderBy('nombre_siembra', 'desc')
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
			->paginate($nro_results);

		$registros = Registro::select()
			->join('siembras', 'registros.id_siembra', 'siembras.id')
			->get();

		$especies_siembra = new EspeciesSiembraController;

		$aux_regs = array();
		$diff = 0;

		if (count($siembras) > 0) {
			foreach ($siembras as $siembra) {

				$especies = EspecieSiembra::select(
					'id_siembra',
					DB::raw("count(id_especie) as nro_especies"),
					DB::raw("sum(cant_actual) as cant_actual"),
					DB::raw("sum(cantidad) as cantidad_inicial"),
					DB::raw("sum(peso_inicial) as peso_inicial"),
					DB::raw("sum(peso_actual) as peso_actual"),
					DB::raw("sum((peso_actual*cant_actual)/1000) as biomasa_disponible"),
					DB::raw("sum((peso_inicial*cantidad)/1000) as biomasa_inicial"),
				)
					->orderBy('especies_siembra.id_siembra')
					->where('id_siembra', $siembra->id)
					->groupBy('id_siembra')
					->first();

				// Especies en la siembra
				if (($especies)) {
					$contador_esp = 0;

					$siembra->contador_esp = $especies->nro_especies;
					$siembra->cantidad_inicial = $especies->cantidad_inicial;
					$siembra->peso_inicial = $especies->peso_inicial;
					$siembra->peso_actual = $especies->peso_actual;
					$siembra->peso_incremento = $especies->peso_actual - $especies->peso_inicial;
					$siembra->biomasa_inicial = $especies->biomasa_inicial;
					$siembra->mortalidad = $especies_siembra->cantidadTotalEspeciesSiembra($especies->id_siembra)->mortalidad ?? 0;
					$siembra->biomasa = $especies_siembra->cantidadTotalEspeciesSiembra($especies->id_siembra)->biomasa ?? 0;
					$siembra->cantidad_SM = $especies_siembra->cantidadTotalEspeciesSiembra($especies->id_siembra)->cantidad ?? 0;
					$siembra->salida_animales = $siembra->cantidad_SM + $siembra->mortalidad;
					$siembra->salida_animales_sin_mortalidad = $siembra->cantidad_SM;
					$siembra->cantidad_actual = $siembra->cantidad_inicial - $siembra->salida_animales;
					$siembra->biomasa_disponible = ($siembra->peso_actual * $siembra->cantidad_actual) / 1000;
					$siembra->mortalidad_kg = $especies_siembra->cantidadTotalEspeciesSiembra($especies->id_siembra)->mortalidad_kg ?? 0;
					$siembra->biomasa_incremento = ($siembra->peso_incremento * $siembra->cantidad_actual) / 1000;

					if ($siembra->id == $especies->id_siembra) {
						$contador_esp++;
						for ($k = 0; $k < count($registros); $k++) {
							if ($especies->id_siembra == $registros[$k]->id_siembra) {
								$int_tiempo = Registro::select('fecha_registro')
									->orderBy('fecha_registro', 'desc')
									->where('id_siembra', $siembra->id)
									->first();

								if (isset($int_tiempo['fecha_registro'])) {
									$date1 = new \DateTime($siembra->fecha_inicio);
									$date2 = new \DateTime($int_tiempo['fecha_registro']);
									$diff = $date1->diff($date2);
									$especies->intervalo_tiempo  = $diff->days;
								} else {
									$especies->intervalo_tiempo  = 1;
								}
							}
						}
						$siembra->intervalo_tiempo = $especies->intervalo_tiempo;
						$siembra->intervalo_tiempo_months = $especies->intervalo_tiempo / 30;
						$siembra->ganancia_peso_dia = $siembra->intervalo_tiempo > 0 ?  $siembra->peso_incremento / $siembra->intervalo_tiempo : 0;
						$siembra->mortalidad_porcentaje = (($siembra->mortalidad * 100) / $siembra->cantidad_inicial);
						$siembra->salida_biomasa = $especies_siembra->cantidadTotalEspeciesSiembraSinMortalidad($siembra->id)->biomasa ?? 0;
						$siembra->porc_supervivencia_final = (($siembra->salida_animales_sin_mortalidad * 100) / $siembra->cantidad_inicial);
						$siembra->densidad_inicial = ($siembra->cantidad_inicial / $siembra->capacidad);
						$siembra->densidad_final = ($siembra->salida_animales_sin_mortalidad / $siembra->capacidad);
						$siembra->carga_inicial = ($siembra->biomasa_inicial / $siembra->capacidad);
						$siembra->carga_final = ($siembra->salida_biomasa / $siembra->capacidad);
					}
				}

				$recursos_necesarios_controller = new RecursoNecesarioController();
				$recursos_necesarios_siembra =	$recursos_necesarios_controller->recursosNecesariosPorSiembra($siembra->id);

				foreach ($recursos_necesarios_siembra as $recurso_necesario) {
					$siembra->minutos_hombre = $recurso_necesario->minutos_hombre;
					$siembra->horas_hombre = $siembra->minutos_hombre / 60;
					$siembra->costo_total_recurso =  $recurso_necesario->costo_total_recurso;
					$siembra->cantidad_total_alimento =  $recurso_necesario->cant_tarde + $recurso_necesario->cant_manana;
					$siembra->costo_total_alimento = $recurso_necesario->costo_manana + $recurso_necesario->costo_tarde;
					$siembra->incr_bio_acum_conver =  $recurso_necesario->incr_bio_acum_conver_m + $recurso_necesario->incr_bio_acum_conver_t;
					$siembra->costo_minutos_hombre = ($recurso_necesario->costo_minutos_hombre);
				}

				$siembra->costo_total_siembra = ($siembra->costo_minutos_hombre + $siembra->costo_total_alimento + $siembra->costo_total_recurso);
				$siembra->costo_produccion_final = $siembra->salida_biomasa > 0 ?  $siembra->costo_total_siembra / $siembra->salida_biomasa : 0;
				$siembra->conversion_alimenticia_siembra = $siembra->incremento_biomasa > 0 ? $siembra->cantidad_total_alimento /  $siembra->incremento_biomasa : 0;
				$siembra->conversion_alimenticia_parcial = (($siembra->biomasa_disponible - $siembra->biomasa_inicial) > 0) ? $siembra->cantidad_total_alimento / ($siembra->biomasa_disponible - $siembra->biomasa_inicial) : 0;

				if (($siembra->salida_biomasa + $siembra->mortalidad_kg - $siembra->biomasa_inicial) > 0) {
					$siembra->conversion_final = (($siembra->cantidad_total_alimento) / ($siembra->salida_biomasa + $siembra->mortalidad_kg - $siembra->biomasa_inicial));
				} else {
					$siembra->conversion_final = 0;
				}
				$siembra->bio_dispo_conver = ($siembra->biomasa_inicial + $siembra->incr_bio_acum_conver) - ($siembra->biomasa_disponible + $siembra->mortalidad_kg);
				$siembra->peso_actual_esp = ($siembra->contador_esp) > 0 ? $siembra->peso_actual / $siembra->contador_esp : 0;

				//Variables reasignadas
				$siembra->cant_actual = $siembra->cantidad_actual;
				$siembra->costo_minutosh = $siembra->costo_minutos_hombre;
				$siembra->costo_tot = $siembra->costo_total_siembra;
				$siembra->peso_actual = $siembra->peso_actual_esp;
			}
		}
		return ['existencias' => $siembras];
	}

	public function getTotalExistences($results)
    {
        $data = [
            'cantidad_inicial' => $results->sum('cantidad_inicial'),
            'cantidad_actual' => $results->sum('cantidad_actual'),
            'peso_inicial' => $results->sum('peso_inicial'),
            'mortalidad' => $results->sum('mortalidad'),
            'mortalidad_kg' => $results->sum('mortalidad_kg'),
            'salida_animales_sin_mortalidad' => $results->sum('salida_animales_sin_mortalidad'),
            'biomasa_disponible' => $results->sum('biomasa_disponible'),
            'salida_biomasa' => $results->sum('salida_biomasa'),
        ];

        return $data;
    }
}
