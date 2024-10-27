<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Repositories\CategoryRepositoryInterface;

class CategoryController extends Controller
{
    protected $categoryRepository;

    /**
     * __construct
     *
     * @param  mixed $categoryRepository
     * @return void
     */
    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $category = $this->categoryRepository->all($search, $status);

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
        $category = $this->categoryRepository->create($validateData);

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
        $category = $this->categoryRepository->find($category);
        $products = $category->product()->limit(10)->get();

        return response()->json([
            'category' => $category,
            'products' => $products
        ]);
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
        $category = $this->categoryRepository->update($category, $validateData);

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
        $this->categoryRepository->delete($category);

        return response()->json($category);
    }
}
