<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wildside\Userstamps\Userstamps;

class ItuElement extends Model
{
    use Userstamps;

    protected $fillable = [
        'title',
        'incident_type_id',
    ];

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }
}
