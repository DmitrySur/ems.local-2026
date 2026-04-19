<?php

namespace App\Models\DropVoltage;

use App\Enum\DropVoltageStatuses;
use App\Models\Directories\GroupInfrastructureObject;
use App\Models\Incident\Incident;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class DropVoltage extends Model
{
    use SoftDeletes, Userstamps;

    protected $fillable = [
        'datetime_drop',
        'group_infrastructure_object_id',
        'detail_location',
        'detail_drop',
        'status_drop',
    ];

    protected $casts = [
        'datetime_drop' => 'datetime',
    ];

    public function groupInfrastructureObject(): BelongsTo
    {
        return $this->belongsTo(GroupInfrastructureObject::class);
    }

    public function dropVoltageEventChronicles(): HasMany
    {
        return $this->hasMany(DropVoltageEventChronicles::class, 'drop_voltage_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'drop_voltage_id');
    }

    public function dropVoltageDevices(): HasMany
    {
        return $this->hasMany(DropVoltageDevices::class, 'drop_voltage_id');
    }

    public function scopeGetAllOpenedDropVoltage(Builder $query, ?array $arrayIdGroupInfObjects): Builder
    {
        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher') && is_array($arrayIdGroupInfObjects)) {
            $query->whereIn('group_infrastructure_object_id', $arrayIdGroupInfObjects);
        }
        return $query->where('status_drop', DropVoltageStatuses::Opened->value);
    }

    public function scopeGetAllOpenedDropVoltagePerDay(Builder $query, ?array $arrayIdGroupInfObjects): Builder
    {
        $nowDate = Carbon::now();
        $endDate = Carbon::now()->day($nowDate->day - 1)->hour(8)->minute(0)->second(0);
        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher') && is_array($arrayIdGroupInfObjects)) {
            $query->whereIn('group_infrastructure_object_id', $arrayIdGroupInfObjects);
        }
        return $query->where('status_drop', DropVoltageStatuses::Opened->value)
            ->whereBetween('datetime_drop', [$endDate, $nowDate]);
    }

    public function scopeGetAllClosedDropVoltage(Builder $query, ?array $arrayIdGroupInfObjects): Builder
    {
        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher') && is_array($arrayIdGroupInfObjects)) {
            $query->whereIn('group_infrastructure_object_id', $arrayIdGroupInfObjects);
        }
        return $query->where('status_drop', DropVoltageStatuses::Closed->value);
    }

    public function scopeGetAllClosedDropVoltagePerDay(Builder $query, ?array $arrayIdGroupInfObjects): Builder
    {
        $nowDate = Carbon::now();
        $endDate = Carbon::now()->day($nowDate->day - 1)->hour(8)->minute(0)->second(0);
        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher') && is_array($arrayIdGroupInfObjects)) {
            $query->whereIn('group_infrastructure_object_id', $arrayIdGroupInfObjects);
        }
        return $query->where('status_drop', DropVoltageStatuses::Closed->value)
            ->whereBetween('datetime_drop', [$endDate, $nowDate]);
    }
}
