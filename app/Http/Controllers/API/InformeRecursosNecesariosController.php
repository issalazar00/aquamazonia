<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RecursoNecesario;
use App\RecursoSiembra;
use App\Alimento;
use App\Recursos;
use App\Siembra;
use App\Actividad;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\Environment\Console;

class InformeRecursosNecesariosController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $recursosNecesarios = RecursoNecesario::select(
        'actividad',
        'id_siembra',
        'id_contenedor',
        'tipo_actividad',
        'nombre_siembra',
        'siembras.estado as estado',
        DB::raw('SUM(cantidad_recurso) as cantidad_recurso'),
        DB::raw('SUM(cant_manana) as c_manana'),
        DB::raw('SUM(cant_tarde) as c_tarde'),
        DB::raw('SUM(minutos_hombre) as minutos_hombre'),
        DB::raw('SUM(horas_hombre) as horas_hombre'),
        DB::raw("SUM(minutos_hombre*costo_minutos_hombre) AS costo_minutos_hombre"),
      )
      ->join('siembras', 'recursos_necesarios.siembra_id', 'siembras.id')
      ->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
      ->groupBy('siembras.nombre_siembra')
      ->groupBy('siembras.estado')
      ->groupBy('siembras.id_contenedor')
      ->groupBy('recursos_necesarios.tipo_actividad')
      ->groupBy('actividades.actividad')
      ->orderBy('nombre_siembra', 'desc')
      ->paginate(30);


    foreach ($recursosNecesarios as $recursoNecesario) {

      $costo_recursos = RecursoNecesario::select()
        ->join('actividades', 'recursos_necesarios.tipo_actividad', 'actividades.id')
        ->leftJoin('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id')
        ->leftJoin('recursos', 'recursos_necesarios.id_recurso', 'recursos.id')
        ->join('siembras', 'recursos_necesarios.siembra_id', 'siembras.id')
        ->get();

      foreach ($costo_recursos as $costo_recurso) {

        if ($recursoNecesario->id_siembra == $costo_recurso->id_siembra) {

          $recursoNecesario->minutosHombre += $costo_recurso->minutos_hombre ;
          $recursoNecesario->costoRecurso +=  ($costo_recurso->cantidad_recurso * $costo_recurso->costo);
          $recursoNecesario->cantidadAlimento += ($costo_recurso->cant_tarde + $costo_recurso->cant_manana);
          $recursoNecesario->costoAlimento +=  (($costo_recurso->cant_tarde + $costo_recurso->cant_manana) * $costo_recurso->costo_kg);
          $recursoNecesario->costoMinutos = ($recursoNecesario->costo_minutos_hombre);
          $recursoNecesario->costoTotalSiembra = $recursoNecesario->costoRecurso + $recursoNecesario->costoMinutos + $recursoNecesario->costoAlimento;

          if ($recursoNecesario->tipo_actividad == $costo_recurso->tipo_actividad) {
            $recursoNecesario->costo_recurso +=  ($costo_recurso->cantidad_recurso * $costo_recurso->costo);
            $recursoNecesario->cantidad_alimento += ($costo_recurso->cant_tarde + $costo_recurso->cant_manana);
            $recursoNecesario->costo_alimento +=  (($costo_recurso->cant_tarde + $costo_recurso->cant_manana) * $costo_recurso->costo_kg);
            $recursoNecesario->costo_minutos = ($recursoNecesario->costo_minutos_hombre) ;
            $recursoNecesario->costo_total_actividad = $recursoNecesario->costo_recurso + $recursoNecesario->costo_minutos + $recursoNecesario->costo_alimento;
          }

          $recursoNecesario->porcentaje_total_produccion = ($recursoNecesario->costo_total_actividad * 100) / $recursoNecesario->costoTotalSiembra;
        }
      }

      $recursoNecesario->horas_hombre = number_format($recursoNecesario->minutos_hombre / 60, 2, ',', '');
      $recursoNecesario->costo_recurso = number_format($recursoNecesario->costo_recurso, 2, ',', '');
      $recursoNecesario->costo_total_actividad = number_format($recursoNecesario->costo_total_actividad, 2, ',', '');
      $recursoNecesario->porcentaje_total_produccion = number_format($recursoNecesario->porcentaje_total_produccion, 2, ',', '');
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
