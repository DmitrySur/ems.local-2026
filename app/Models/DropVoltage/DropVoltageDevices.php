<?php

namespace App\Models\DropVoltage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DropVoltageDevices extends Model
{
    protected $fillable = [
        'type',
        'name',
        'status',
        'drop_voltage_id',
        'comment'
    ];

    public function dropVoltage(): BelongsTo
    {
        return $this->belongsTo(DropVoltage::class);
    }
}
