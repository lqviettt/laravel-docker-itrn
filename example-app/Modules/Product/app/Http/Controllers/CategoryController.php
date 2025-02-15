<?php

namespace Modules\Product\Http\Controllers;

use App\Contract\CategoryRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Product\Models\Category;

class CategoryController extends Controller
{
    /**
     * __construct
     *
     * @param  mixed $categoryRepository
     * @return void
     */
    public function __construct(protected CategoryRepositoryInterface $categoryRepository) {}

    /**
     * index
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 50);
        $this->authorize('view', Category::class);
        $query = $this->categoryRepository
            ->builderQuery()
            ->searchByName($request->search)
            ->searchByStatus($request->status);

        return $this->sendSuccess($query->paginate($perPage));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        // $this->authorize('create', Category::class);
        $validateData = $request->validated();
        $category = $this->categoryRepository->create($validateData);

        return $this->created($category);
    }

    /**
     * show
     *
     * @param  mixed $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        // $this->authorize('view', Category::class);
        $category = $this->categoryRepository->find($category);

        return $this->sendSuccess($category);
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
        // $this->authorize('update', $category);
        $validateData = $request->validated();
        $this->categoryRepository->update($category, $validateData);

        return $this->updated($category);
    }

    /**
     * destroy
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        // $this->authorize('delete', $category);
        $category->delete($category);

        return $this->deteled();
    }
}
