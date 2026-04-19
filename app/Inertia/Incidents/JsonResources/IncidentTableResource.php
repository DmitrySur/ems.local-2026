<?php

namespace App\Inertia\Incidents\JsonResources;

use App\Enum\TypesInfrastructureObjectEnum;
use APP\Models\Incident\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Incident
 */
class IncidentTableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'incident_date' => $this?->datetime_incident?->format('d.m.Y') ?? null,
            'incident_time' => $this?->datetime_incident?->format('H:i') ?? null,
            'reported_by' => $this->reported_by ?? null,
            'incident_classification' => $this?->incident_classification ?? null,
            'number_nnr' => $this?->number_nnr ?? null,
            'division_short_name' => $this->relationLoaded('division') ? $this?->division?->short_name : null,
            'object_infrastructure' =>
                $this->objectInfrastructure?->type ?
                    trim((TypesInfrastructureObjectEnum::
                        tryFrom($this->objectInfrastructure->type)?->getShortLabel() ?? '')
                        . ' ' . $this->objectInfrastructure->name) : null,
            'location' => $this->location ?? null,
            'detail_location' => $this->detail_location ?? null,
            'incident_type' => $this->relationLoaded('incidentType') ? $this?->incidentType?->title : null,
            'is_has_directory_objects' => $this->relationLoaded('incidentType') ? $this?->incidentType?->has_directory_objects : null,
            'itu_specie_title' => $this->relationLoaded('ituSpecie') ? $this?->ituSpecie?->title : null,
            'directory_specie_title' => $this->whenLoaded('ituDirectoryObject', function () {
                if ($this->ituDirectoryObject->relationLoaded('ituSpecie')) {
                    return $this->ituDirectoryObject->ituSpecie->title;
                }
                return null;
            }),
            'directory_title' => $this->relationLoaded('ituDirectoryObject') ? $this?->ituDirectoryObject?->title : null,
            'itu_characteristic_title' => $this->relationLoaded('ituCharacteristic') ? $this?->ituCharacteristic?->title : null,
            'detail_object_incident' => $this->detail_object_incident ?? null,
            'itu_fault_title' => $this->relationLoaded('ituFault') ? $this?->ituFault?->title : null,
            'itu_element_title' => $this->relationLoaded('ituElement') ? $this?->ituElement?->title : null,
            'itu_reason_breakdown_title' => $this->relationLoaded('ituReasonBreakdown') ? $this?->ituReasonBreakdown?->title : null,
            'creator_name' => $this->relationLoaded('creator') ? $this?->creator?->name : null,
            'updater_name' => $this->relationLoaded('editor') ? $this?->editor?->name : null,
            'created_at' => $this?->created_at?->format('d.m.Y H:i') ?? null,
            'updated_at' => $this?->updated_at?->format('d.m.Y H:i') ?? null,
            'status_resolution' => $this->status_resolution ?? null,
            'repair_date' => $this->repair_date?->format('d.m.Y H:i') ?? null,
            'appropriate_measures' => $this->appropriate_measures ?? null,
            'detail_incident' => $this->detail_incident ?? null,
        ];
    }
}
