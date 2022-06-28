<?php

namespace App\Http\Controllers;

use App\Phase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'phases' => Phase::paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'phase' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'code' =>  400,
                'message' => 'Validación de datos incorrecta',
                'errors' =>  $validate->errors()
            ], 400);
        }

        $phase = Phase::create([
            'phase' => $request->input('phase')

        ]);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Registro exitoso',
            'phase' => $phase
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Phase  $phase
     * @return \Illuminate\Http\Response
     */
    public function show(Phase $phase)
    {
        if ($phase) {
            $data = [
                'status' => 'success',
                'code' => 200,
                'phase' => $phase
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Registro no encontrado'
            ];
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Phase  $phase
     * @return \Illuminate\Http\Response
     */
    public function edit(Phase $phase)
    {
        abort(404);
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Phase  $phase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Phase $phase)
    {
        $validate = Validator::make($request->all(), [
            'phase' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'code' =>  400,
                'message' => 'Validación de datos incorrecta',
                'errors' =>  $validate->errors()
            ], 400);
        }

        // $phase = Phase::find($id);

        if ($phase) {
            $phase->phase = $request->input('phase');

            $phase->save();
            $data = [
                'status' => 'success',
                'code' =>  200,
                'message' => 'Actualización exitosa',
                'phase' =>  $phase
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' =>  400,
                'message' => 'Registro no encontrado',
            ];
        }
        return response()->json($data, $data['code']);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Phase  $phase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Phase $phase)
    {
        // $phase = Tax::find($id);

        if ($phase) {
            $phase->delete();
            $data = [
                'status' => 'success',
                'code' => 200,
                'phase' => $phase
            ];
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Registro no encontrado'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
