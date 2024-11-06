<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{        
    /**
     * getPermission
     *
     * @return JsonResponse
     */
    public function getPermission(): JsonResponse
    {
        $permission = Permission::all();

        return response()->json($permission);
    }
    
    /**
     * editPermission
     *
     * @param  mixed $request
     * @param  mixed $employee
     * @return JsonResponse
     */
    public function editPermission(Request $request, Employee $employee): JsonResponse
    {
        $data = $request->validate([
            'permission_id' => [
                'required',
                Rule::unique('employee_permissions')->where('employee_id', $employee->id),
            ],
        ]);

        $employee->permissions()->attach($data['permission_id']);
        $employee->load('permissions');

        return response()->json($employee);
    }
    
    /**
     * showPermissions
     *
     * @param  mixed $employee
     * @return JsonResponse
     */
    public function showPermissions(Employee $employee): JsonResponse
    {
        $permissions = $employee->permissions()->get();

        return response()->json($permissions);
    }
    
    /**
     * removePermission
     *
     * @param  mixed $request
     * @param  mixed $employee
     * @return JsonResponse
     */
    public function removePermission(Request $request, Employee $employee): JsonResponse
    {
        $request->validate([
            'permission_id' => 'required|exists:permissions,id',
        ]);

        $employee->permissions()->detach($request->permission_id);
        $employee->load('permissions');

        return response()->json($employee);
    }
}
