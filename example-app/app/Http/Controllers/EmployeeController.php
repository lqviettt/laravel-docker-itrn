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
        $query = $this->employeeRepository
            ->builderQuery()
            ->searchByNameCode($request->search)
            ->searchByPhone($request->phone);

        return response()->json($query->paginate(10)->makeHidden(['created_at', 'updated_at']));
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function store(EmployeeRequest $request): JsonResponse
    {
        // $va = $request->validated();

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
        $category = $this->employeeRepository->delete($employee);

        return response()->json($category);
    }
}
