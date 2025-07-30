<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Redis;
use Predis\Client;

class ServiceReadyController
{
    public function __invoke(): JsonResponse
    {
        $result = Process::run('git log --pretty="%H" -n1 HEAD');
        /** @var Client $redis */
        $redis = Redis::connection()->client();

        $data = [
            'is_ready' => true,
            'status' => 200,
            'git' => mb_rtrim($result->output(), PHP_EOL),
        ];

        try {
            DB::connection()->getPdo();
            $redis->connect();
        } catch (Exception $ex) {
            $data['status'] = 503;
            $data['is_ready'] = false;
            if (config('app.debug')) {
                $data['error'] = $ex->getMessage();
            }
        }

        return new JsonResponse(['data' => $data], $data['status']);
    }
}
