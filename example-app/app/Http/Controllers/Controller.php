<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * sendSuccess
     *
     * @param  mixed $data
     * @param  string $message
     * @param  int $code
     * @return JsonResponse
     */
    public function sendSuccess(mixed $data, string $message = 'Successfully', int $code = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'status' => $code,
            'message' => $message
        ]);
    }

    /**
     * sendError
     *
     * @param  string $message
     * @param  int $code
     * @return JsonResponse
     */
    public function sendError(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'error' => $message
        ]);
    }

    /**
     * created
     *
     * @param  mixed $data
     * @param  string $message
     * @param  int $code
     * @return JsonResponse
     */
    public function created(mixed $data, string $message = 'Create Successfully', int $code = 201): JsonResponse
    {
        return $this->sendSuccess($data, $message, $code);
    }

    /**
     * updated
     *
     * @param  mixed $data
     * @param  string $message
     * @return JsonResponse
     */
    public function updated(mixed $data, string $message = 'Update Successfully'): JsonResponse
    {
        return $this->sendSuccess($data, $message);
    }

    /**
     * deteled
     *
     * @param  string $message
     * @return JsonResponse
     */
    public function deteled(string $message = 'Delete Successfully'): JsonResponse
    {
        return $this->sendSuccess('', $message);
    }
}
