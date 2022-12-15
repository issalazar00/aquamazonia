<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RecursoNecesario;
use Illuminate\Support\Facades\DB;

class InformeRecursosNecesariosController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {

    $id_siembra = 'siembra_id';
    $operador_siembra = '!=';
    $filtro_siembra = '-1';
    $idTipoActividad = 'tipo_actividad';
    $signoIdTipoActividad = '!=';
    $valorIdTipoActividad = '-1';
    $c5 = 'estado';
    $op3 = '!=';
    $c6 = '-1';
    $signCont = '!=';
    $idContenedor = '-1';

    if ($request['f_siembra'] != '-1') {
      $id_siembra = "siembra_id";
      $operador_siembra = '=';
      $filtro_siembra = $request['f_siembra'];
    }
    if ($request['search_activity'] != '-1') {
      $idTipoActividad = "tipo_actividad";
      $signoIdTipoActividad = '=';
      $valorIdTipoActividad = $request['search_activity'];
    }
    if ($request['f_estado'] != '-1') {
      $c5 = "estado";
      $op3 = '=';
      $c6 = $request['f_estado'];
    }
    if ($request['f_contenedor'] != '-1') {
      $signCont = '=';
      $idContenedor = $request['f_contenedor'];
    }

    $recursosNecesarios = RecursoNecesario::select(
      'actividad',
      'siembra_id',
      'tipo_actividad',
      'nombre_siembra',
      'estado',
      DB::raw('SUM(cantidad_recurso) as cantidad_recurso'),
      DB::raw('SUM(cant_manana) as c_manana'),
      DB::raw('SUM(cant_tarde) as c_tarde'),
      DB::raw('SUM(minutos_hombre) as minutos_hombre'),
      DB::raw('SUM(horas_hombre) as horas_hombre'),
      DB::raw('SUM(minutos_hombre * costo_minutos_hombre) as costo_minutos'),
      DB::raw('SUM(cant_manana * costo_alimento) as c_manana_costo'),
      DB::raw('SUM(cant_tarde * costo_alimento) as c_tarde_costo'),
      DB::raw('SUM(cantidad_recurso * costo_recurso) as costo_recurso'),
    )
      ->join('siembras', 'recursos_necesarios.siembra_id', 'siembras.id')
      ->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
      ->groupBy('siembras.nombre_siembra', 'siembra_id','estado')
      ->groupBy('recursos_necesarios.tipo_actividad')
      ->groupBy('actividades.actividad')
      ->where($id_siembra, $operador_siembra, $filtro_siembra)
      ->where($idTipoActividad, $signoIdTipoActividad, $valorIdTipoActividad)
      ->where($c5, $op3, $c6)
      ->where('siembras.id_contenedor', $signCont, $idContenedor)
      ->orderBy('nombre_siembra', 'desc')
      ->orderBy('tipo_actividad', 'asc')
      ->paginate(30);

    foreach ($recursosNecesarios as $recursoNecesario) {
      $recursoNecesario->cantidad_alimento = $recursoNecesario->c_manana + $recursoNecesario->c_tarde;
      $recursoNecesario->costo_alimento = $recursoNecesario->c_manana_costo + $recursoNecesario->c_tarde_costo;
      $recursoNecesario->costo_total_actividad = $recursoNecesario->costo_recurso + $recursoNecesario->costo_minutos + $recursoNecesario->costo_alimento;
      $recursoNecesario->costo_total_siembra = $this->costoTotalSiembra($recursoNecesario->siembra_id);
      $recursoNecesario->porcentaje_total_produccion = ($recursoNecesario->costo_total_actividad * 100) / $recursoNecesario->costo_total_siembra;
    }

    return ['recursosNecesarios' => $recursosNecesarios];
  }

  public function costoTotalSiembra($siembra_id)
  {
    $recursosNecesarios = RecursoNecesario::select(

      DB::raw('SUM(cantidad_recurso) as cantidad_recurso'),
      DB::raw('SUM(cant_manana) as c_manana'),
      DB::raw('SUM(cant_tarde) as c_tarde'),
      DB::raw('SUM(minutos_hombre) as minutos_hombre'),
      DB::raw('SUM(horas_hombre) as horas_hombre'),
      DB::raw('SUM(minutos_hombre * costo_minutos_hombre) as costo_minutos'),
      DB::raw('SUM(cant_manana * costo_alimento) as c_manana_costo'),
      DB::raw('SUM(cant_tarde * costo_alimento) as c_tarde_costo'),
      DB::raw('SUM(cantidad_recurso * costo_recurso) as costo_recurso'),
    )
      ->groupBy('siembra_id')
      ->where('siembra_id', $siembra_id)
      ->get();

    $costo_total_siembra = 0;
    foreach ($recursosNecesarios as $recursoNecesario) {
      $recursoNecesario->cantidad_alimento = $recursoNecesario->c_manana + $recursoNecesario->c_tarde;
      $recursoNecesario->costo_alimento = $recursoNecesario->c_manana_costo + $recursoNecesario->c_tarde_costo;
      $costo_total_siembra = $recursoNecesario->costo_recurso + $recursoNecesario->costo_minutos + $recursoNecesario->costo_alimento;
    }
    return $costo_total_siembra;
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
  }
}
