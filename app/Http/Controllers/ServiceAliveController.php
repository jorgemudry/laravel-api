<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ServiceAliveController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): JsonResponse
    {
        $data = [
            'is_alive' => true,
            'status' => 200,
        ];

        return new JsonResponse(['data' => $data], $data['status']);
    }
}
