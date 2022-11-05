<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\EspecieSiembra;
use App\Siembra;
use App\RecursoNecesario;
use App\Recursos;
use App\Registro;
use App\Actividad;
use Illuminate\Support\Facades\DB;

class InfomeBiomasaAlimentoController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */

  public function index(Request $request)
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

    $siembras = Siembra::select(
      'siembras.id as id',
      'capacidad',
      'nombre_siembra',
      'id_contenedor',
      'fecha_inicio',
      'ini_descanso',
      'siembras.estado',
      'fin_descanso'
    )
      ->where($id_siembra, $operador_id_siembra, $valorIdSiembra)
      // ->where('id_contenedor', $signCont, $valorIdContenedor)
      ->where('siembras.estado', $op2, $c4)
      ->join('contenedores', 'siembras.id_contenedor', 'contenedores.id')
      ->orderBy('nombre_siembra', 'desc')
      ->get();

    $registros = Registro::select()
      ->join('siembras', 'registros.id_siembra', 'siembras.id')
      ->get();

    $mh = Recursos::select()->where('recurso', 'Minutos hombre')->orWhere('recurso', 'Minuto hombre')->orWhere('recurso', 'Minutos')->first();

    $especies_siembra = new EspeciesSiembraController;

    $aux_regs = array();
    $diff = 0;

    if (count($siembras) > 0) {
      foreach ($siembras as $siembra) {
        $especies = $especies_siembra->generalInfoEspeciesSiembra($siembra->id);

        // Especies en la siembra
        if (($especies)) {
          $contador_esp = 0;

          $siembra->contador_esp = $especies->nro_especies;
          $siembra->cantidad_inicial = $especies->cantidad_inicial;
          $siembra->peso_ini = $especies->peso_inicial;
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
        $recursos_necesarios_siembra =  $recursos_necesarios_controller->recursosNecesariosPorSiembra($siembra->id);

        foreach ($recursos_necesarios_siembra as $recurso_necesario) {
          $siembra->minutos_hombre = $recurso_necesario->minutos_hombre;
          $siembra->horas_hombre = $siembra->minutos_hombre / 60;
          $siembra->costo_total_recurso =  $recurso_necesario->costo_total_recurso;
          $siembra->cantidad_total_alimento =  $recurso_necesario->cant_tarde + $recurso_necesario->cant_manana;
          $siembra->costo_total_alimento = $recurso_necesario->costo_manana + $recurso_necesario->costo_tarde;
          $siembra->incr_bio_acum_conver =  $recurso_necesario->incr_bio_acum_conver;
        }

        $siembra->costo_minutos_hombre = ($siembra->minutos_hombre * $mh->costo);
        $siembra->costo_total_siembra = ($siembra->costo_minutos_hombre + $siembra->costo_total_alimento + $siembra->costo_total_recurso);
        $siembra->costo_produccion_final = $siembra->salida_biomasa > 0 ?  $siembra->costo_total_siembra / $siembra->salida_biomasa : 0;
        $siembra->conversion_alimenticia = $siembra->incr_bio_acum_conver > 0 ?  ($siembra->cantidad_total_alimento) / ($siembra->incr_bio_acum_conver) : 0;
        $siembra->conversion_alimenticia_siembra = $siembra->incremento_biomasa > 0 ? $siembra->cantidad_total_alimento /  $siembra->incremento_biomasa : 0;
        $siembra->conversion_alimenticia_parcial = (($siembra->biomasa_disponible - $siembra->biomasa_inicial) > 0) ? $siembra->cantidad_total_alimento / ($siembra->biomasa_disponible - $siembra->biomasa_inicial) : 0;

        // $siembra->bio_dispo_alimen = (($siembra->incr_bio_acum_conver + $siembra->biomasa_inicial) - ($siembra->salida_biomasa + $siembra->mortalidad_kg));
        $siembra->bio_dispo_alimen =$especies->bio_dispo_alimen;

        $siembra->costo_produccion_parcial = $siembra->bio_dispo_alimen > 0 ? $siembra->costo_total_siembra / ($siembra->bio_dispo_alimen + $siembra->salida_biomasa) : 0;

        if (($siembra->salida_biomasa + $siembra->mortalidad_kg - $siembra->biomasa_inicial) > 0) {
          $siembra->conversion_final = (($siembra->cantidad_total_alimento) / ($siembra->salida_biomasa + $siembra->mortalidad_kg - $siembra->biomasa_inicial));
        } else {
          $siembra->conversion_final = 0;
        }
        $siembra->bio_dispo_conver = ($siembra->biomasa_inicial + $siembra->incr_bio_acum_conver) - ($siembra->biomasa_disponible + $siembra->mortalidad_kg);
        $siembra->peso_actual_esp = ($siembra->contador_esp) > 0 ? $siembra->peso_actual / $siembra->contador_esp : 0;

        // recursos_necesarios

      }
    }

    $siembra_to_array = $siembras->toArray();

    $totalizadoSiembras = [];
    $totalizadoSiembras['biomasa_inicial'] = array_sum(array_column($siembra_to_array, 'biomasa_inicial'));
    $totalizadoSiembras['cantidad_actual'] = array_sum(array_column($siembra_to_array, 'cantidad_actual'));
    $totalizadoSiembras['mortalidad'] = array_sum(array_column($siembra_to_array, 'mortalidad'));
    $totalizadoSiembras['mortalidad_kg'] = array_sum(array_column($siembra_to_array, 'mortalidad_kg'));
    $totalizadoSiembras['salida_animales_sin_mortalidad'] = array_sum(array_column($siembra_to_array, 'salida_animales_sin_mortalidad'));
    $totalizadoSiembras['biomasa_disponible'] = array_sum(array_column($siembra_to_array, 'biomasa_disponible'));
    $totalizadoSiembras['salida_biomasa'] = array_sum(array_column($siembra_to_array, 'salida_biomasa'));
    $totalizadoSiembras['cantidad_total_alimento'] = array_sum(array_column($siembra_to_array, 'cantidad_total_alimento'));
    $totalizadoSiembras['incr_bio_acum_conver'] = array_sum(array_column($siembra_to_array, 'incr_bio_acum_conver'));
    $totalizadoSiembras['bio_dispo_alimen'] = array_sum(array_column($siembra_to_array, 'bio_dispo_alimen'));
    $totalizadoSiembras['costo_minutos_hombre'] = array_sum(array_column($siembra_to_array, 'costo_minutos_hombre'));
    $totalizadoSiembras['costo_total_recurso'] = array_sum(array_column($siembra_to_array, 'costo_total_recurso'));
    $totalizadoSiembras['costo_total_alimento'] = array_sum(array_column($siembra_to_array, 'costo_total_alimento'));
    $totalizadoSiembras['costo_total_siembra'] = array_sum(array_column($siembra_to_array, 'costo_total_siembra'));

    return ['existencias' => $siembras, 'totalizadoSiembras' => $totalizadoSiembras];
  }
}
