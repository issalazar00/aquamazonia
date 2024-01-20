<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $brand = $request->brand;

        $brands = Brand::orderBy('brand', 'asc')
        ->where(function ($query) use ($brand) {
			if (!is_null($brand) && $brand != '' && $brand != 'all') {
				$query->where('brand', 'LIKE',  "%$brand%");
			}
		})
        ->paginate(10);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'brands' => $brands
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
     * @param  \App\Http\Requests\StoreBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create([
            'brand' => $request->brand,
            'state' => $request->state
        ]);

        return $brand;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function edit(Brand $brand)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBrandRequest  $request
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        if ($brand) {
            $brand->brand = $request->input('brand');
            $brand->state = $request->input('state');
            $brand->save();
    
            $data = [
                'status' => 'success',
                'code' =>  200,
                'message' => 'Actualización exitosa',
                'brand' =>  $brand
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
     * @param  \App\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        if ($brand) {
            $brand->delete();
            $data = [
                'status' => 'success',
                'code' => 200,
                'brand' => $brand
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
