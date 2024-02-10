<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Alimento;
use App\HistorialAlimento;

class AlimentosController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    //
    $alimentos = Alimento::all();
    return $alimentos;
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $val = $request->validate([
      'alimento' => 'required',
      'costo_kg' => 'required',
      'warehouse_id'=> 'nullable',
      'category_id'=> 'nullable',
      'brand_id'=> 'nullable',
      'provider_id'=> 'nullable'
    ]);
    $alimento = Alimento::create([
      'alimento' => $request['alimento'],
      'costo_kg' => $request['costo_kg'],
      'warehouse_id' => $request['warehouse_id'],
      'category_id' => $request['category_id'],
      'brand_id' => $request['brand_id'],
      'provider_id' => $request['provider_id']
    ]);

    HistorialAlimento::create([
      'id_alimento' => $alimento['id'],
      'costo' => $alimento['costo_kg'],
      'fecha_registro' => date('Y-m-d')
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    abort(404);
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
    $alimento = Alimento::findOrFail($id);
    $alimento->update($request->all());

    HistorialAlimento::create([
      'id_alimento' => $request['id'],
      'costo' => $request['costo_kg'],
      'fecha_registro' => date('Y-m-d')
    ]);

    return $alimento;
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
    Alimento::destroy($id);
    HistorialAlimento::where('id_alimento', $id)->delete();

    return 'eliminado';
  }
}
