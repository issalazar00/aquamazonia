<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\RecursoNecesario;
use App\RecursoSiembra;
use App\Alimento;
use App\Recursos;

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
        $recursosNecesarios = RecursoNecesario::orderBy('fecha_ra', 'desc')->get();
      
        $recursosSiembra = RecursoSiembra::select('recursos_siembras.id as id', 'id_registro', 'id_siembra', 'id_recurso', 'id_alimento', 'fecha_ra', 'horas_hombre', 'cant_manana', 'cant_tarde', 'detalles', 'tipo_actividad', 'recursos_necesarios.id as idrn', 'nombre_siembra', 'alimento', 'recurso')
        ->join('recursos_necesarios', 'recursos_siembras.id_registro', 'recursos_necesarios.id')
        ->join('siembras', 'recursos_siembras.id_siembra', 'siembras.id')
        ->join('recursos', 'recursos_necesarios.id_recurso', 'recursos.id')
        ->join('alimentos', 'recursos_necesarios.id_alimento','alimentos.id')
        ->get();
        
        $registrosxSiembra=array();
        
        foreach($recursosSiembra as $rs){
            $registrosxSiembra[$rs['id_registro']][$rs['id']] = array('id_registro' => $rs['id_registro'], 'id_siembra' => $rs['id_siembra'], 'id_recurso' => $rs['id_recurso'], 'id_alimento' => $rs['id_alimento'], 'fecha_ra'=>$rs['fecha_ra'], 'horas_hombre' => $rs['horas_hombre'], 'cant_manana' => $rs['cant_manana'], 'cant_tarde'=>$rs['cant_tarde'], 'detalles' => $rs['detalles'], 'tipo_actividad'=> $rs['tipo_actividad'], 'idrn' => $rs['idrn']);
        }
        
        return ['recursosNecesarios' => $recursosNecesarios, 'recursosSiembra' => $recursosSiembra, 'registrosxSiembra' => $registrosxSiembra];
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
        $recursoNecesario = new RecursoNecesario();
        $recursoNecesario->id_recurso = $request['id_recurso'];
        $recursoNecesario->id_alimento =  $request['id_alimento'];
        $recursoNecesario->tipo_actividad = $request['tipo_actividad'];
        $recursoNecesario->fecha_ra = $request['fecha_ra'];
        $recursoNecesario->horas_hombre = $request['horas_hombre'];
        $recursoNecesario->cant_manana = $request['cant_manana'];
        $recursoNecesario->cant_tarde = $request['cant_tarde'];
        $recursoNecesario->detalles = $request['detalles'];
        $recursoNecesario->save();
        
       foreach ($request->id_siembra as $siembra){
            $recursoSiembra = new RecursoSiembra();
            $recursoSiembra->id_registro = $recursoNecesario->id;
            $recursoSiembra->id_siembra = $siembra;
            $recursoSiembra->save();
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
    public function searchResults(Request $request){
        $c1 = "tipo_actividad"; $op1="!="; $c2 = "-1";
        $c3 = "tipo_actividad"; $op2="!=";  $c4="-3";
        $c5 = "tipo_actividad"; $op3="!=";  $c6="-1";
        
        if($request['tipo_actividad']!='1'){$c1="tipo_actividad"; $op1='='; $c2= $request['tipo_actividad'];}
        if($request['fecha_ra1']!='-3'){$c3="fecha_ra"; $op2='>='; $c4=$request['fecha_ra1'];}
        if($request['fecha_ra2']!='-1'){$c5="fecha_ra"; $op3='<='; $c6=$request['fecha_ra2'];}
        
        $recursosNecesarios = RecursoNecesario::orderBy('fecha_ra', 'desc')
        ->where($c1, $op1, $c2)
        ->where($c3, $op2, $c4)
        ->where($c5, $op3, $c6)
        ->get();
      
        $recursosSiembra = RecursoSiembra::select('recursos_siembras.id as id', 'id_registro', 'id_siembra', 'id_recurso', 'id_alimento', 'fecha_ra', 'horas_hombre', 'cant_manana', 'cant_tarde', 'detalles', 'tipo_actividad', 'recursos_necesarios.id as idrn', 'nombre_siembra', 'alimento', 'recurso')
        ->join('recursos_necesarios', 'recursos_siembras.id_registro', 'recursos_necesarios.id')
        ->join('siembras', 'recursos_siembras.id_siembra', 'siembras.id')
        ->join('recursos', 'recursos_necesarios.id_recurso', 'recursos.id')
        ->join('alimentos', 'recursos_necesarios.id_alimento','alimentos.id')        
        ->get();
        
        $registrosxSiembra=array();
        
        foreach($recursosSiembra as $rs){
            $registrosxSiembra[$rs['id_registro']][$rs['id']] = array('id_registro' => $rs['id_registro'], 'id_siembra' => $rs['id_siembra'], 'id_recurso' => $rs['id_recurso'], 'id_alimento' => $rs['id_alimento'], 'fecha_ra'=>$rs['fecha_ra'], 'horas_hombre' => $rs['horas_hombre'], 'cant_manana' => $rs['cant_manana'], 'cant_tarde'=>$rs['cant_tarde'], 'detalles' => $rs['detalles'], 'tipo_actividad'=> $rs['tipo_actividad'], 'idrn' => $rs['idrn']);
        }
        
        return ['recursosNecesarios' => $recursosNecesarios, 'recursosSiembra' => $recursosSiembra, 'registrosxSiembra' => $registrosxSiembra];
   
    }
}
