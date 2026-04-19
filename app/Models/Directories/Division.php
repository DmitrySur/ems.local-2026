<?php

namespace App\Models\Directories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Wildside\Userstamps\Userstamps;

class Division extends Model implements Sortable
{
    use Userstamps, SortableTrait;

    protected $fillable = [
        'name',
        'short_name',
        'has_group_object',
        'created_by',
        'updated_by',
        'group_infrastructure_object_id',
        'report_position'
    ];

    public array $sortable = [
        'order_column_name' => 'report_position',
        'sort_when_creating' => true,
    ];

    public function groupInfrastructureObject(): BelongsTo
    {
        return $this->belongsTo(GroupInfrastructureObject::class);
    }
}
