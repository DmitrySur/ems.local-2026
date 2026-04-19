<?php

namespace App\Models\Inspection;

use App\Models\Directories\ObjectInfrastructure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjectInfrastructureInspectionItem extends Model
{
    protected $fillable = [
        'object_infrastructure_id',
        'inspection_id'
    ];
    protected $table = 'inf_objs_inspections_pivot';
    public $timestamps = false;

    public function objectInfrastructure(): BelongsTo
    {
        return $this->belongsTo(ObjectInfrastructure::class);
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }
}
