<?php

namespace App\Models\Inspection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspector extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'short_name',
        'default_position',
    ];
}
