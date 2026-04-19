<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wildside\Userstamps\Userstamps;

class ItuCharacteristic extends Model
{
    use Userstamps;

    protected $fillable = [
        'incident_type_id',
        'title',
    ];

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }
}
