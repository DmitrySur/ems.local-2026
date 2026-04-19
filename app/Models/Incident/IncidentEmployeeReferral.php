<?php

namespace App\Models\Incident;

use App\Models\Directories\IncidentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wildside\Userstamps\Userstamps;

class IncidentEmployeeReferral extends Model
{
    use Userstamps;

    protected $fillable = [
        'position',
        'fio',
        'direction_time',
        'arrival_time',
        'incident_type_id'
    ];

    protected $casts = [
        'direction_time' => 'datetime: H:i',
        'arrival_time' => 'datetime: H:i',
    ];

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }
}
