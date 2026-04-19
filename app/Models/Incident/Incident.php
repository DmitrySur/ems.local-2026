<?php

namespace App\Models\Incident;

use App\Enum\IncidentStatuses;
use App\Models\Directories\DispatchArea;
use App\Models\Directories\Division;
use App\Models\Directories\IncidentType;
use App\Models\Directories\ItuCharacteristic;
use App\Models\Directories\ItuDirectoryObject;
use App\Models\Directories\ItuElement;
use App\Models\Directories\ItuFault;
use App\Models\Directories\ItuReasonBreakdown;
use App\Models\Directories\ItuSpecie;
use App\Models\Directories\ObjectInfrastructure;
use App\Models\DropVoltage\DropVoltage;
use App\Services\Incident\Reports\IncidentsReportsConfigEnum\TypeIncidentsStrategy;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class Incident extends Model
{
    use Userstamps, SoftDeletes;

    protected $fillable = [
        'datetime_incident',
        'object_infrastructure_id',
        'location',
        'detail_location',
        'reported_by',
        'division_id',
        'incident_type_id',
        'itu_specie_id',
        'itu_characteristic_id',
        'itu_directory_object_id',
        'itu_fault_id',
        'itu_element_id',
        'itu_reason_breakdown_id',
        'detail_object_incident',
        'detail_incident',
        'incident_classification',
        'number_nnr',
        'appropriate_measures',
        'note',
        'status_resolution',
        'status_incident',
        'dispatch_area_id',
        'repair_date',
        'is_in_report'
    ];

    protected $casts = [
        'datetime_incident' => 'datetime',
        'repair_date' => 'datetime'
    ];

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }

    public function ituSpecie(): BelongsTo
    {
        return $this->belongsTo(ItuSpecie::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function dispatchArea(): BelongsTo
    {
        return $this->belongsTo(DispatchArea::class);
    }

    public function ituCharacteristic(): BelongsTo
    {
        return $this->belongsTo(ItuCharacteristic::class);
    }

    public function ituDirectoryObject(): BelongsTo
    {
        return $this->belongsTo(ItuDirectoryObject::class);
    }

    public function ituElement(): BelongsTo
    {
        return $this->belongsTo(ItuElement::class);
    }

    public function ituFault(): BelongsTo
    {
        return $this->belongsTo(ItuFault::class);
    }

    public function ituReasonBreakdown(): BelongsTo
    {
        return $this->belongsTo(ItuReasonBreakdown::class);
    }

    public function objectInfrastructure(): BelongsTo
    {
        return $this->belongsTo(ObjectInfrastructure::class);
    }

    public function eventChronicles(): HasMany
    {
        return $this->hasMany(EventChronicles::class, 'incident_id');
    }

    public function incidentEmployeeReferrals(): HasMany
    {
        return $this->hasMany(IncidentEmployeeReferral::class, 'incident_type_id');
    }

    public function incidentEmployeeInformations(): HasMany
    {
        return $this->hasMany(IncidentEmployeeInformation::class, 'incident_id');
    }

    public function dropVoltage(): BelongsTo
    {
        return $this->belongsTo(DropVoltage::class);
    }

    public function scopeAllInRepairWithDispatchAreasPerDay(Builder $query, ?int $dispatchAreaId): Builder
    {
        $query->where('status_resolution', '=', IncidentStatuses::InRepair->value);
        $nowDate = Carbon::now();
        $endDate = Carbon::now()->day($nowDate->day - 1)->hour(8)->minute(0)->second(0);
        $query->whereBetween('datetime_incident', [$endDate, $nowDate]);

        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher')) {
            $query->whereHas('dispatchArea', function (Builder $builder) use ($dispatchAreaId) {
                return $builder->where('id', $dispatchAreaId);
            });
        }
        return $query;
    }

    public function scopeAllInRepairWithDispatchAreas(Builder $query, ?int $dispatchAreaId): Builder
    {
        $query->where('status_resolution', '=', IncidentStatuses::InRepair->value);

        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher')) {
            $query->whereHas('dispatchArea', function (Builder $builder) use ($dispatchAreaId) {
                return $builder->where('id', $dispatchAreaId);
            });
        }
        return $query;
    }

    public function scopeAllPerDay(Builder $query, ?int $dispatchAreaId): Builder
    {
        $nowDate = Carbon::now();
        $endDate = Carbon::now()->day($nowDate->day - 1)->hour(8)->minute(0)->second(0);
        $query->whereBetween('datetime_incident', [$endDate, $nowDate]);

        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher')) {
            $query->whereHas('dispatchArea', function (Builder $builder) use ($dispatchAreaId) {
                return $builder->where('id', $dispatchAreaId);
            });
        }
        return $query;
    }

    public function scopeAllIncidents(Builder $query, ?int $dispatchAreaId): Builder
    {
        //Для роли линейного диспетчера отражать данные только его участка
        if (Auth::user()->hasRole('linear_dispatcher')) {
            $query->whereHas('dispatchArea', function (Builder $builder) use ($dispatchAreaId) {
                return $builder->where('id', $dispatchAreaId);
            });
        }
        return $query;
    }

    public function scopeWithRelationForIncidentListPage(Builder $query): Builder
    {
        return $query->with([
            'incidentType',
            'ituSpecie',
            'division',
            'dispatchArea',
            'ituCharacteristic',
            'ituDirectoryObject',
            'ituElement',
            'ituFault',
            'ituReasonBreakdown',
            'objectInfrastructure',
            'creator',
            'dropVoltage'
        ]);
    }

    public function scopeIncidentsForMainReport(
        Builder $query,
        ?array  $recordsIds,
        ?Carbon $startDateTime,
        ?Carbon $endDateTime
    ): Builder
    {
        return $query->with([
            'incidentType',
            'ituSpecie',
            'division',
            'ituCharacteristic',
            'ituDirectoryObject.ituSpecie',
            'ituElement',
            'ituFault',
            'ituReasonBreakdown',
            'objectInfrastructure',
            'eventChronicles.creator',
            'eventChronicles.editor',
            'incidentEmployeeReferrals',
            'incidentEmployeeInformations',
            'dropVoltage',
            'creator',
            'editor',
        ])
            ->leftJoin('divisions', 'incidents.division_id', '=', 'divisions.id')
            ->leftJoin('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select('incidents.*')
            //выгрузить все которые выбрал диспетчер за сутки
            ->whereIn('incidents.id', $recordsIds)
            //выгрузить все неустраненные до текущих суток
            ->orWhere(function (Builder $subQuery) use ($startDateTime) {
                $subQuery->where('status_resolution', IncidentStatuses::InRepair->value)
                    ->where('datetime_incident', '<', $startDateTime->toDateTimeString());
            })
            //выгрузить все устраненные старые инциденты за текущие сутки
            ->orWhere(function (Builder $subQuery) use ($startDateTime, $endDateTime) {
                $subQuery->whereBetween('repair_date', [$startDateTime, $endDateTime]);
                $subQuery->where('status_resolution', IncidentStatuses::InWorking->value);
            })
            ->orderBy('incident_types.report_position')
            ->orderBy('divisions.report_position')
            ->orderBy('datetime_incident');
    }

    public function scopeIncidentsForCustomizableSummaryIncidentReportServiceByPeriod(
        Builder               $query,
        ?Carbon               $startDateTime,
        ?Carbon               $endDateTime,
    ): Builder
    {
        return $query->with([
            'incidentType',
            'ituSpecie',
            'division',
            'ituCharacteristic',
            'ituDirectoryObject.ituSpecie',
            'ituElement',
            'ituFault',
            'ituReasonBreakdown',
            'objectInfrastructure',
            'eventChronicles.creator',
            'eventChronicles.editor',
            'incidentEmployeeReferrals',
            'incidentEmployeeInformations',
            'dropVoltage',
            'creator',
            'editor',
        ])
            ->leftJoin('divisions', 'incidents.division_id', '=', 'divisions.id')
            ->leftJoin('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select('incidents.*')
            //выгрузить все неустраненные выявленные за сутки
            ->orWhere(function (Builder $subQuery) use ($startDateTime, $endDateTime) {
                $subQuery->where('status_resolution', IncidentStatuses::InRepair->value)
                    ->whereBetween('datetime_incident', [$startDateTime, $endDateTime]);
            })
            //выгрузить все устраненные инциденты за текущие сутки
            ->orWhere(function (Builder $subQuery) use ($startDateTime, $endDateTime) {
                $subQuery->whereBetween('repair_date', [$startDateTime, $endDateTime]);
                $subQuery->where('status_resolution', IncidentStatuses::InWorking->value);
            })
            ->orderBy('incident_types.report_position')
            ->orderBy('divisions.report_position')
            ->orderBy('datetime_incident');
    }
    public function scopeIncidentsForCustomizableSummaryIncidentReportServiceByPeriodNnr(
        Builder               $query,
        ?Carbon               $startDateTime,
        ?Carbon               $endDateTime,
    ): Builder
    {
        return $query->with([
            'incidentType',
            'ituSpecie',
            'division',
            'ituCharacteristic',
            'ituDirectoryObject.ituSpecie',
            'ituElement',
            'ituFault',
            'ituReasonBreakdown',
            'objectInfrastructure',
            'eventChronicles.creator',
            'eventChronicles.editor',
            'incidentEmployeeReferrals',
            'incidentEmployeeInformations',
            'dropVoltage',
            'creator',
            'editor',
        ])
            ->leftJoin('divisions', 'incidents.division_id', '=', 'divisions.id')
            ->leftJoin('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select('incidents.*')
            // фильтр по ННР
            ->whereNotNull('number_nnr')
            ->where('incident_classification', '=', 'ННР')
            ->whereNot('number_nnr', '=', '')
            ->where(function (Builder $subQuery) use ($startDateTime, $endDateTime) {
                $subQuery->where(function (Builder $innerQuery) use ($startDateTime, $endDateTime) {
                    $innerQuery->where('status_resolution', IncidentStatuses::InRepair->value)
                        ->whereBetween('datetime_incident', [$startDateTime, $endDateTime]);
                })
                    ->orWhere(function (Builder $innerQuery) use ($startDateTime, $endDateTime) {
                        $innerQuery->whereBetween('repair_date', [$startDateTime, $endDateTime])
                            ->where('status_resolution', IncidentStatuses::InWorking->value);
                    });
            })
            ->orderBy('incident_types.report_position')
            ->orderBy('divisions.report_position')
            ->orderBy('datetime_incident');
    }

    public function scopeIncidentsForCustomizableSummaryIncidentReportServiceInRepairAll(
        Builder               $query,
    ): Builder
    {
        return $query->with([
            'incidentType',
            'ituSpecie',
            'division',
            'ituCharacteristic',
            'ituDirectoryObject.ituSpecie',
            'ituElement',
            'ituFault',
            'ituReasonBreakdown',
            'objectInfrastructure',
            'eventChronicles.creator',
            'eventChronicles.editor',
            'incidentEmployeeReferrals',
            'incidentEmployeeInformations',
            'dropVoltage',
            'creator',
            'editor',
        ])
            ->leftJoin('divisions', 'incidents.division_id', '=', 'divisions.id')
            ->leftJoin('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select('incidents.*')
            ->where('status_resolution', IncidentStatuses::InRepair->value)
            ->orderBy('incident_types.report_position')
            ->orderBy('divisions.report_position')
            ->orderBy('datetime_incident');
    }

    public function scopeIncidentsForNnrReport(
        Builder $query,
        ?Carbon $startDateTime,
        ?Carbon $endDateTime
    ): Builder
    {
        return $query->with([
            'incidentType',
            'ituSpecie',
            'division',
            'ituCharacteristic',
            'ituDirectoryObject.ituSpecie',
            'ituElement',
            'ituFault',
            'ituReasonBreakdown',
            'objectInfrastructure',
            'eventChronicles.creator',
            'eventChronicles.editor',
            'incidentEmployeeReferrals',
            'incidentEmployeeInformations',
            'dropVoltage',
            'creator',
            'editor',
        ])
            ->leftJoin('divisions', 'incidents.division_id', '=', 'divisions.id')
            ->leftJoin('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select('incidents.*')
            ->whereBetween('datetime_incident', [$startDateTime, $endDateTime])
            ->where('incident_classification', '=', 'ННР')
            ->whereNotNull('number_nnr')
            ->whereNot('number_nnr', '=', '')
            ->orderBy('incident_types.report_position')
            ->orderBy('divisions.report_position')
            ->orderBy('datetime_incident');
    }
}
