<?php

declare(strict_types=1);

arch('app classes use strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('v1 controllers are invokable')
    ->expect('App\Http\Controllers\V1')
    ->toBeInvokable();

arch('enums implement ToArrayEnum')
    ->expect('App\Enums')
    ->toImplement('App\Contracts\ToArrayEnum');

arch('no debugging functions in app code')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->not->toBeUsed();

arch('exceptions extend the correct base classes')
    ->expect('App\Exceptions')
    ->toExtend('Symfony\Component\HttpKernel\Exception\HttpException')
    ->ignoring([
        'App\Exceptions\ExceptionMapper',
        'App\Exceptions\ErrorResponseBuilder',
        'App\Exceptions\FlattenException',
    ]);
