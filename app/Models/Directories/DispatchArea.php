<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DispatchArea extends Model
{
    protected $fillable = [
        'name',
        'group_infrastructure_object_id'
    ];

    public function groupInfrastructureObject(): BelongsTo
    {
        return $this->belongsTo(GroupInfrastructureObject::class);
    }

    public function groupInfrastructureObjects(): BelongsToMany
    {
        return $this->belongsToMany(GroupInfrastructureObject::class, 'disp_ar_gr_infr_obj_pivot');
    }
}
