<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjectInfrastructure extends Model
{
    protected $fillable = [
        'name',
        'type',
        'created_by',
        'updated_by',
    ];

    public function groupInfrastructureObject(): BelongsTo
    {
        return $this->belongsTo(GroupInfrastructureObject::class);
    }
}
