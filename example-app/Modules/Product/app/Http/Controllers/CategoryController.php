<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Repositories\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Models\Category;

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
        $perPage = $request->input('perPage', 5);
        $this->authorize('view', Category::class);
        $query = $this->categoryRepository
            ->builderQuery()
            ->searchByName($request->search)
            ->searchByStatus($request->status);

        return response()->json($query->paginate($perPage));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);
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
        $this->authorize('view', Category::class);
        $category = $this->categoryRepository->find($category);

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
        $this->authorize('update', $category);
        $validateData = $request->validated();
        $this->categoryRepository->update($category, $validateData);

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
        $this->authorize('delete', $category);
        $category = $this->categoryRepository->delete($category);

        return response()->json($category);
    }
}
