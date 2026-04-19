<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Wildside\Userstamps\Userstamps;

class GroupInfrastructureObject extends Model
{
    use Userstamps;

    protected $fillable = [
        'title',
        'short_title',
    ];

    public function objectInfrastructures(): HasMany
    {
        return $this->hasMany(ObjectInfrastructure::class, 'group_infrastructure_object_id');
    }

    public function dispatchAreas(): BelongsToMany
    {
        return $this->belongsToMany(DispatchArea::class, 'disp_ar_gr_infr_obj_pivot');
    }

    public function division(): HasOne
    {
        return $this->hasOne(Division::class, 'group_infrastructure_object_id');
    }
}
