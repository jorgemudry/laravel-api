<?php

declare(strict_types=1);

namespace App\Enums;

use App\Contracts\ToArrayEnum;
use App\Traits\HasToArrayEnum;

enum Status: string implements ToArrayEnum
{
    use HasToArrayEnum;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
