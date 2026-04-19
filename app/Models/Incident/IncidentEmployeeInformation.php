<?php

namespace App\Models\Incident;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wildside\Userstamps\Userstamps;

class IncidentEmployeeInformation extends Model
{
    use Userstamps;

    protected $fillable = [
        'position',
        'fio',
        'information_time',
        'created_by',
        'updated_by',
        'incident_id'
    ];

    protected $casts = [
        'information_time' => 'datetime: H:i'
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
