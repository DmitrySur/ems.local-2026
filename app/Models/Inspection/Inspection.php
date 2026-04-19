<?php

namespace App\Models\Inspection;

use App\Models\Directories\Division;
use App\Models\Directories\ObjectInfrastructure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Inspection extends Model
{
    use Userstamps, SoftDeletes;

    protected $fillable = [
        'position',
        'inspector_id',
        'type',
        'date_start',
        'division_id',
        'start_time',
        'end_time',
        'subdivisions'
    ];

    protected $casts = [
        'date_start' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'subdivisions' => 'array'
    ];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function objectInfrastructures(): BelongsToMany
    {
        return $this->belongsToMany(ObjectInfrastructure::class, 'inf_objs_inspections_pivot');
    }

    public function objectInfrastructureInspectionItems(): HasMany
    {
        return $this->hasMany(ObjectInfrastructureInspectionItem::class, 'inspection_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }


}
