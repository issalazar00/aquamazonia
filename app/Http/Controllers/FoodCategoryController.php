<?php

namespace App\Http\Controllers;

use App\FoodCategory;
use App\Http\Requests\FoodCategory\StoreFoodCategoryRequest;
use App\Http\Requests\FoodCategory\UpdateFoodCategoryRequest;
use Illuminate\Http\Request;

class FoodCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category = $request->category;

        $foodCategories = FoodCategory::orderBy('category', 'asc')
        ->where(function ($query) use ($category) {
			if (!is_null($category) && $category != '' && $category != 'all') {
				$query->where('category', 'LIKE',  "%$category%");
			}
		})
        ->paginate(10);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'foodCategories' => $foodCategories
        ], 200);
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
     * @param  \App\Http\Requests\StoreFoodCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFoodCategoryRequest $request)
    {
        $food_category = FoodCategory::create([
            'category' => $request->category,
            'state' => $request->state
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'foodCategory' => $food_category
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\FoodCategory  $foodCategory
     * @return \Illuminate\Http\Response
     */
    public function show(FoodCategory $foodCategory)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FoodCategory  $foodCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(FoodCategory $foodCategory)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFoodCategoryRequest  $request
     * @param  \App\FoodCategory  $foodCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFoodCategoryRequest $request, FoodCategory $foodCategory)
    {
        
        if ($foodCategory) {
            $foodCategory->category = $request->input('category');
            $foodCategory->state = $request->input('state');
            $foodCategory->save();
    
            $data = [
                'status' => 'success',
                'code' =>  200,
                'message' => 'Actualización exitosa',
                'foodCategory' =>  $foodCategory
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
     * @param  \App\FoodCategory  $foodCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(FoodCategory $foodCategory)
    {
        if ($foodCategory) {
            $foodCategory->delete();
            $data = [
                'status' => 'success',
                'code' => 200,
                'foodCategory' => $foodCategory
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