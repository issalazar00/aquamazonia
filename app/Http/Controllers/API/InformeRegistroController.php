<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\EspeciesSiembraController;
use Illuminate\Http\Request;
use App\Registro;
use App\EspecieSiembra;
use App\RecursoNecesario;
use App\Siembra;

class InformeRegistroController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{

		$c1 = 'registros.id';
		$op1 = '!=';
		$c2 = '-1';
		$c3 = 'registros.tipo_registro';
		$op2 = '!=';
		$c4 = '-1';
		$c5 = 'registros.id';
		$op3 = '!=';
		$c6 = '-1';
		$c7 = 'registros.id';
		$op4 = '!=';
		$c8 = '-1';
		$c9 = 'registros.id';
		$op5 = '!=';
		$c10 = '-1';
		$filtroPesoGanadoDesde = "registros.peso_ganado";
		$signoPesoGanadoDesde = '!=';
		$valorPesoGanadoDesde = '0';
		$filtroPesoGanadoHasta = "registros.peso_ganado";
		$signoPesoGanadoHasta = '!=';
		$valorPesoGanadoHasta = '0';
		$c15 = 'lote';
		$op8 = '!=';
		$c16 = '-1';

		$estado_siembra = '-1';
		$filtro_estado_siembra = '!=';

		$filtro_contenedor = '!=';
		$id_contenedor = '-1';


		if ($request['f_siembra'] != '-1') {
			$c1 = "registros.id_siembra";
			$op1 = '=';
			$c2 = $request['f_siembra'];
		}
		if ($request['search_activity'] != '-1') {
			$c3 = "registros.tipo_registro";
			$op2 = '=';
			$c4 = $request['search_activity'];
		}
		if ($request['search_from'] != '-1') {
			$c5 = "fecha_registro";
			$op3 = '>=';
			$c6 = $request['search_from'];
		}
		if ($request['search_to'] != '-1') {
			$c7 = "fecha_registro";
			$op4 = '<=';
			$c8 = $request['search_to'];
		}
		if ($request['f_especie'] != '-1') {
			$c9 = "especies.id";
			$op5 = '=';
			$c10 = $request['f_especie'];
		}
		if ($request['f_peso_d'] != '-1') {
			$filtroPesoGanadoDesde = "peso_ganado";
			$signoPesoGanadoDesde = '>=';
			$valorPesoGanadoDesde = $request['f_peso_d'];
		}
		if ($request['f_peso_h'] != '-1') {
			$filtroPesoGanadoHasta = "peso_ganado";
			$signoPesoGanadoHasta = '<=';
			$valorPesoGanadoHasta = $request['f_peso_h'];
		}
		if ($request['f_lote'] != '-1') {
			$c15 = "lote";
			$op8 = '=';
			$c16 = $request['f_lote'];
		}
		if ($request['f_estado'] != '-1') {
			$filtro_estado_siembra = '=';
			$estado_siembra = $request['f_estado'];
		}
		if ($request['id_contenedor'] != '-1') {
			$filtro_contenedor = '=';
			$id_contenedor = $request['id_contenedor'];
		}

		$registros = Registro::select(
			'registros.id as id',
			'registros.id_siembra as id_siembra',
			'registros.id_especie as id_especie',
			'fecha_registro',
			'tipo_registro',
			'peso_ganado',
			'registros.cantidad',
			'mortalidad',
			'especies.especie as especie',
			'nombre_siembra',
			'lote',
			'especies_siembra.cantidad as cantidad_inicial',
			'especies_siembra.cant_actual as cantidad_actual',
			'especies_siembra.peso_actual as peso_actual',
			'siembras.id_contenedor'
		)
			->join(
				'especies',
				'registros.id_especie',
				'especies.id'
			)
			->join('siembras', 'registros.id_siembra', 'siembras.id')
			->leftJoin('especies_siembra', function ($join) {
				$join->on('registros.id_especie', '=', 'especies_siembra.id_especie')->on('registros.id_siembra', '=', 'especies_siembra.id_siembra');
			})
			->where($c1, $op1, $c2)
			->where($c3, $op2, $c4)
			->where($c5, $op3, $c6)
			->where($c7, $op4, $c8)
			->where($c9, $op5, $c10)
			->where($c15, $op8, $c16)
			->where($filtroPesoGanadoDesde, $signoPesoGanadoDesde, $valorPesoGanadoDesde)
			->where($filtroPesoGanadoHasta, $signoPesoGanadoHasta, $valorPesoGanadoHasta)
			->where('siembras.estado', $filtro_estado_siembra, $estado_siembra)
			->where('siembras.id_contenedor', $filtro_contenedor, $id_contenedor)
			// ->where('tipo_registro', '<>', 0)
			->orderBy('fecha_registro', 'desc')
			->paginate(30);

		$especies_siembra = new EspeciesSiembraController;
		if (count($registros) > 0) {
			foreach ($registros as $registro) {

				$registro->mortalidad_general = $especies_siembra->cantidadEspecieSiembra($registro->id_siembra, $registro->id_especie)->mortalidad;
				$registro->biomasa_general = $especies_siembra->cantidadEspecieSiembra($registro->id_siembra, $registro->id_especie)->biomasa;
				$registro->salida_animales_general = $especies_siembra->cantidadEspecieSiembraSinMortalidad($registro->id_siembra, $registro->id_especie)->cantidad ;
				$registro->cantidad_actual = $registro->cantidad_inicial - $registro->salida_animales_general;
				$registro->biomasa_disponible = ((($registro->peso_actual) * ($registro->cantidad_actual)) / 1000);
				$registro->biomasa_inicial =  ((($registro->peso_inicial) * ($registro->cantidad_inicial)) / 1000);

				$registro->bio_dispo_alimen = $especies_siembra->generalInfoEspeciesSiembra($registro->id_siembra)['bio_dispo_alimen'];
				// $registro->salida_animales = $registro->cantidad + $registro->mortalidad;
				$registro->salida_animales = $registro->cantidad;
				$registro->biomasa = ($registro->cantidad * $registro->peso_actual) / 1000;
				if ($registro->tipo_registro == 0) $registro->nombre_registro = 'Muestreo';
				if ($registro->tipo_registro == 1) $registro->nombre_registro = 'Pesca';
				if ($registro->tipo_registro == 2) $registro->nombre_registro = 'Mortalidad Inicial';
				if ($registro->tipo_registro == 3) $registro->nombre_registro = 'Peso Inicial';
			}
		}
		return $registros;
	}

	public function destroy($id, Request $request)
	{
		//
		$registro = Registro::destroy($id);
	}
}
