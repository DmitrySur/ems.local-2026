<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Wildside\Userstamps\Userstamps;

class IncidentType extends Model implements Sortable
{
    use Userstamps, SortableTrait;

    protected $fillable = [
        'has_species',
        'has_characteristic',
        'has_elements',
        'has_faults',
        'has_directory_objects',
        'title',
        'reported_by_list',
        'report_position'
    ];

    public array $sortable = [
        'order_column_name' => 'report_position',
        'sort_when_creating' => true,
    ];

    protected $casts = [
        'reported_by_list' => 'json'
    ];

    public function ituCharacteristics(): HasMany
    {
        return $this->hasMany(ItuCharacteristic::class, 'incident_type_id');
    }

    public function ituDirectoryObjects(): HasMany
    {
        return $this->hasMany(ItuDirectoryObject::class, 'incident_type_id');
    }

    public function ituElements(): HasMany
    {
        return $this->hasMany(ItuElement::class, 'incident_type_id');
    }

    public function ituFaults(): HasMany
    {
        return $this->hasMany(ItuFault::class, 'incident_type_id');
    }

    public function ituSpecies(): HasMany
    {
        return $this->hasMany(ItuSpecie::class, 'incident_type_id');
    }

    public function ituReasonBreakdowns(): HasMany
    {
        return $this->hasMany(ItuReasonBreakdown::class, 'incident_type_id');
    }
}
