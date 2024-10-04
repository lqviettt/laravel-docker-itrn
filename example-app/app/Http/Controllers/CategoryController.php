<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $category = Category::all()->makeHidden(['created_at', 'updated_at']);

        return response()->json($category);
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validateData = $request->validate([
            "name" => ["required"]
        ]);

        $category = Category::query()->create($validateData);

        return response()->json($category);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::with('products')->find($id);

        return response()->json($category);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validateData = $request->validate([
            "name" => ["required"]
        ]);

        $category = Category::query()
            ->find($id)
            ->update($validateData);

        return response()->json($category);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::query()->find($id);
        $category->delete();

        return response()->json($category);
    }
}
