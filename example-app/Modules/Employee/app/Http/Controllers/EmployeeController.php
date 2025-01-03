<?php

namespace Modules\Employee\Http\Controllers;

use App\Contract\EmployeeRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Employee\Models\Employee;

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

        return $this->sendSuccess($query->paginate($perPage));
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

        return $this->created($employee);
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

        return $this->sendSuccess($employee);
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

        return $this->updated($employee);
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
        $employee->delete();

        return $this->deteled();
    }
}
