<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Prometheus\Prom;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Prometheus\RenderTextFormat;

/**
 * @link https://fly.io/laravel-bytes/instrument-laravel-for-prometheus/
 */
final class PrometheusMetricsController extends Controller
{
    public function __invoke(Request $request): ResponseFactory|Response
    {
        $formatter = new RenderTextFormat();

        return response(
            $formatter->render(Prom::getMetricFamilySamples()),
            200,
            [
                'Content-Type' => RenderTextFormat::MIME_TYPE,
            ]
        );
    }
}
