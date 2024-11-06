<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Repositories\EmployeeRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeRepository;

    /**
     * __construct
     *
     * @param  mixed $employeeRepository
     * @return void
     */
    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * index
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('perPage', 5);
        $this->authorize('view', Employee::class);
        $query = $this->employeeRepository
            ->builderQuery()
            ->searchByNameCode($request->search)
            ->searchByPhone($request->phone);

        return response()->json($query->paginate($perPage));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        $this->authorize('create', Employee::class);
        $employee = $this->employeeRepository->create($request->newEmployee());

        return response()->json($employee);
    }

    /**
     * show
     *
     * @param  mixed $employee
     * @return JsonResponse
     */
    public function show(Employee $employee): JsonResponse
    {
        $this->authorize('view', $employee);
        $employee = $this->employeeRepository->find($employee);

        return response()->json($employee);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $employee
     * @return JsonResponse
     */
    public function update(EmployeeRequest $request, Employee $employee): JsonResponse
    {
        $this->authorize('update', $employee);
        $this->employeeRepository->update($employee, $request->updateEmployee());

        return response()->json($employee);
    }

    /**
     * destroy
     *
     * @param  mixed $employee
     * @return JsonResponse
     */
    public function destroy(Employee $employee): JsonResponse
    {
        $this->authorize('delete', $employee);
        $category = $this->employeeRepository->delete($employee);

        return response()->json($category);
    }
}
