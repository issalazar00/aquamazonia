<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\EspecieSiembra;
use App\RecursoNecesario;
use App\Registro;
use App\Siembra;
use stdClass;

class EspeciesSiembraController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    //
    if (isset($request['idSiembra'])) {
      $especies_siembra = EspecieSiembra::select('especies_siembra.id as id', 'id_siembra', 'id_especie', 'lote', 'cantidad', 'peso_inicial', 'cant_actual',  'peso_actual', 'especies.especie as especie',)
        ->join('especies', 'especies_siembra.id_especie', 'especies.id')
        ->where('id_siembra', $request['idSiembra'])
        ->orderBy('especie', 'asc')
        ->get()->toArray();
    }

    return $especies_siembra;
  }

  /**
   * Se devuelve la cantidad total de especies en una siembra
   * A partir del conteo de los registros
   */
  public function cantidadTotalEspeciesSiembra($id_siembra)
  {
    return Registro::select(
      'id_siembra',
      DB::raw('SUM(cantidad) as cantidad'),
      DB::raw('SUM(mortalidad) as mortalidad'),
      DB::raw('SUM((mortalidad * peso_ganado) /1000 ) as mortalidad_kg'),
      DB::raw('SUM(biomasa) as biomasa')
    )
      ->where('id_siembra', $id_siembra)
      ->groupBy('id_siembra')
      ->first();
  }
  public function cantidadTotalEspeciesSiembraSinMortalidad($id_siembra)
  {
    return Registro::select(
      'id_siembra',
      DB::raw('SUM(cantidad) as cantidad'),
      DB::raw('SUM(biomasa) as biomasa'),
      DB::raw('SUM((mortalidad * peso_ganado) /1000 ) as mortalidad_kg'),
    )
      ->where('id_siembra', $id_siembra)
      ->where('tipo_registro', '<>', 0)

      ->groupBy('id_siembra')
      ->first();
  }
  /**
   * Se devuelve la cantidad total de cada especie en una siembra
   * A partir del conteo de los registros
   */
  public function cantidadEspecieSiembra($id_siembra, $id_especie)
  {
    return Registro::select(
      'id_siembra',
      'id_especie',
      DB::raw('SUM(cantidad) as cantidad'),
      DB::raw('SUM(mortalidad) as mortalidad'),
      DB::raw('SUM((mortalidad * peso_ganado) /1000 ) as mortalidad_kg'),
      DB::raw('SUM(biomasa) as biomasa')
    )
      ->where('id_siembra', $id_siembra)
      ->where('id_especie', $id_especie)
      ->groupBy('id_siembra')
      ->groupBy('id_especie')
      ->first();
  }

  public function cantidadEspecieSiembraSinMortalidad($id_siembra, $id_especie)
  {
    return Registro::select(
      'id_siembra',
      'id_especie',
      DB::raw('SUM(cantidad) as cantidad'),
      DB::raw('SUM(biomasa) as biomasa')
    )
      ->where('id_siembra', $id_siembra)
      ->where('id_especie', $id_especie)
      ->where('tipo_registro', '<>', 0)
      ->groupBy('id_siembra')
      ->groupBy('id_especie')
      ->first();
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

  public function generalInfoEspeciesSiembra($id_siembra)
  {
    $siembras = Siembra::select(
      'siembras.id as id',
      'nombre_siembra'
    )
      ->where('siembras.id', $id_siembra)
      ->get();

    $existencias = EspecieSiembra::select(
      'cant_actual',
      'especies_siembra.cantidad as cantidad_inicial',
      'especies_siembra.id_especie as id_especie',
      'especies_siembra.id_siembra as id_siembra',
      'peso_inicial',
      'peso_actual',
    )
      ->where('id_siembra', $id_siembra)
      ->get();

    $registros = Registro::select()
      ->join('siembras', 'registros.id_siembra', 'siembras.id')->where('siembras.id', $id_siembra)
      ->get();

    $recursos_necesarios = RecursoNecesario::select(
      'recursos_necesarios.id as id_registro',
      'siembra_id',
      'id_alimento',
      'cant_manana',
      'cant_tarde',
      'conv_alimenticia',
    )
      ->leftJoin('alimentos', 'recursos_necesarios.id_alimento', 'alimentos.id')
      ->leftJoin('recursos', 'recursos_necesarios.id_recurso', 'recursos.id')
      ->where('siembra_id', $id_siembra)
      ->get();

    $especies_siembra = new EspeciesSiembraController;

    if (count($siembras) > 0) {
      foreach ($siembras as $siembra) {
        $siembra->id_siembra = $siembra->id;
        if (count($existencias) > 0) {
          foreach ($existencias as $existencia) {
            $siembra->cantidad_inicial += $existencia->cantidad_inicial;
            $siembra->peso_actual += $existencia->peso_actual;
            // $siembra->cantidad_inicial += $existencia->cantidad_inicial;
            $existencia->biomasa_inicial =  ((($existencia->peso_inicial) * ($existencia->cantidad_inicial)) / 1000);
            $siembra->biomasa_inicial += $existencia->biomasa_inicial;

            foreach ($registros as $registro) {
                if ($existencia->id_siembra == $registro->id_siembra) {
              $existencia->salida_biomasa += $registro->biomasa;
              if ($existencia->id_especie == $registro->id_especie) {
                $registro->mortalidad_kg = (($registro->mortalidad * $registro->peso_ganado) / 1000);
                $existencia->mortalidad += $registro->mortalidad;
                $existencia->mortalidad_kg += $registro->mortalidad_kg;
                $existencia->salida_biomasa_especie += $registro->biomasa;
              }
            }
              }
            $siembra->mortalidad += $existencia->mortalidad;
            $siembra->mortalidad_kg += $existencia->mortalidad_kg;
            $siembra->salida_biomasa = $especies_siembra->cantidadTotalEspeciesSiembraSinMortalidad($siembra->id)->biomasa ?? 0;
          }
        }

        foreach ($recursos_necesarios as $recurso_necesario) {
          $recurso_necesario->cantidad_total_alimento = $recurso_necesario->cant_tarde + $recurso_necesario->cant_manana;
          $siembra->cantidad_total_alimento +=  $recurso_necesario->cantidad_total_alimento;
          $siembra->cant_tarde +=  $recurso_necesario->cant_tarde;
          $siembra->cant_manana +=  $recurso_necesario->cant_manana;

          if ($recurso_necesario->conv_alimenticia > 0) {
            $recurso_necesario->incr_bio_acum_conver = $recurso_necesario->cantidad_total_alimento / $recurso_necesario->conv_alimenticia;
            $siembra->incr_bio_acum_conver +=  $recurso_necesario->incr_bio_acum_conver;
          }
        }

        $siembra->bio_dispo_alimen = (($siembra->incr_bio_acum_conver + $siembra->biomasa_inicial) - ($siembra->salida_biomasa + $siembra->mortalidad_kg));

        return $siembra;
      }
    }
  }
}
