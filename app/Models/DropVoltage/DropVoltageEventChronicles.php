<?php

namespace App\Models\DropVoltage;

use App\Models\Incident\Incident;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wildside\Userstamps\Userstamps;

class DropVoltageEventChronicles extends Model
{
    use Userstamps;

    protected $fillable = [
        'datetime_event',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'datetime_event' => 'datetime',
    ];

    public function dropVoltage(): BelongsTo
    {
        return $this->belongsTo(DropVoltage::class);
    }
}
