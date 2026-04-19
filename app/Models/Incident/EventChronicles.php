<?php

namespace App\Models\Incident;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wildside\Userstamps\Userstamps;

class EventChronicles extends Model
{
    use Userstamps;

    protected $fillable = [
        'datetime_event',
        'description',
        'is_show_in_reports',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'datetime_event' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
