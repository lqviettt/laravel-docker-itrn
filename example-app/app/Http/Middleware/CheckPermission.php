<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    public function handle($request, Closure $next, $entity, $action)
    {
        $user = auth()->user();

        if (!$user || !$user->employee) {
            abort(403, "Bạn không có quyền truy cập đâu e.");
        }

        $permissions = $user->employee->permissions
            ->where('entity', $entity)
            ->pluck('name')
            ->toArray();

        if (!in_array($action, $permissions)) {
            return response()->json(["Role" => "Bạn không có quyền $action đối với $entity."]);
        }

        return $next($request);
    }
}
