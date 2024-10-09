<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $perPage = $request->input('per_page', 10);

        $category = Category::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', 'like', '%' . $status . '%');
            })
            ->select('id', 'name', 'status')
            ->paginate($perPage);

        return response()->json($category->makeHidden(['created_at', 'updated_at']));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $validateData = $request->validated();
        $category = Category::query()->create($validateData);

        return response()->json($category);
    }

    /**
     * show
     *
     * @param  mixed $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('products');

        return response()->json($category);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return JsonResponse
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $validateData = $request->validated();
        $category->update($validateData);

        return response()->json($category);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json($category);
    }
}
