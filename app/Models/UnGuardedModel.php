<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class UnGuardedModel extends Model
{
    use HasUlids;

    /**
     * List of attributes for which mass assignment is forbidden.
     */
    protected $guarded = [];
}
