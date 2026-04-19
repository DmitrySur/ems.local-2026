<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wildside\Userstamps\Userstamps;

class ItuDirectoryObject extends Model
{
    use Userstamps;

    protected $fillable = [
        'title',
        'incident_type_id',
        'itu_specie_id',
    ];

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }

    public function ituSpecie(): BelongsTo
    {
        return $this->belongsTo(ItuSpecie::class);
    }
}
