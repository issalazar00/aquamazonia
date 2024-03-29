<?php

namespace App\Http\Controllers;

use App\ResourceCategory;
use App\Http\Requests\ResourceCategory\StoreResourceCategoryRequest;
use App\Http\Requests\ResourceCategory\UpdateResourceCategoryRequest;
use Illuminate\Http\Request;

class ResourceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category = $request->category;

        $resourceCategories = ResourceCategory::orderBy('category', 'asc')
        ->where(function ($query) use ($category) {
			if (!is_null($category) && $category != '' && $category != 'all') {
				$query->where('category', 'LIKE',  "%$category%");
			}
		})
        ->paginate(10);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'resourceCategories' => $resourceCategories
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
     * @param  \App\Http\Requests\StoreResourceCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreResourceCategoryRequest $request)
    {
        $resource_category = ResourceCategory::create([
            'category' => $request->category,
            'state' => $request->state
        ]);

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'resourceCategory' => $resource_category
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ResourceCategory  $resourceCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceCategory $resourceCategory)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ResourceCategory  $resourceCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(ResourceCategory $resourceCategory)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateResourceCategoryRequest  $request
     * @param  \App\ResourceCategory  $resourceCategory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateResourceCategoryRequest $request, ResourceCategory $resourceCategory)
    {
        
        if ($resourceCategory) {
            $resourceCategory->category = $request->input('category');
            $resourceCategory->state = $request->input('state');
            $resourceCategory->save();
    
            $data = [
                'status' => 'success',
                'code' =>  200,
                'message' => 'Actualización exitosa',
                'resourceCategory' =>  $resourceCategory
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
     * @param  \App\ResourceCategory  $resourceCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ResourceCategory $resourceCategory)
    {
        if ($resourceCategory) {
            $resourceCategory->delete();
            $data = [
                'status' => 'success',
                'code' => 200,
                'resourceCategory' => $resourceCategory
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
