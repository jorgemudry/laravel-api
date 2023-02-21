<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ServiceAliveController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $data = [
            'is_alive' => true,
            'status' => 200,
        ];

        return new JsonResponse(['data' => $data], $data['status']);
    }
}
