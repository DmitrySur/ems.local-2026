<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Directories\DispatchArea;
use App\Models\Directories\Division;
use App\Models\Directories\GroupInfrastructureObject;
use App\Models\Directories\IncidentType;
use App\Models\Directories\ItuCharacteristic;
use App\Models\Directories\ItuDirectoryObject;
use App\Models\Directories\ItuElement;
use App\Models\Directories\ItuFault;
use App\Models\Directories\ItuReasonBreakdown;
use App\Models\Directories\ItuSpecie;
use App\Models\Directories\ObjectInfrastructure;
use App\Models\DropVoltage\DropVoltage;
use App\Models\DropVoltage\DropVoltageDevices;
use App\Models\DropVoltage\DropVoltageEventChronicles;
use App\Models\Incident\EventChronicles;
use App\Models\Incident\Incident;
use App\Models\Incident\IncidentEmployeeInformation;
use App\Models\Incident\IncidentEmployeeReferral;
use App\Models\Inspection\Inspection;
use App\Models\Inspection\Inspector;
use App\Models\User;
use App\Policies\Directories\DispatchAreaPolicy;
use App\Policies\Directories\DivisionPolicy;
use App\Policies\Directories\GroupInfrastructureObjectPolicy;
use App\Policies\Directories\IncidentTypePolicy;
use App\Policies\Directories\ItuCharacteristicPolicy;
use App\Policies\Directories\ItuDirectoryObjectPolicy;
use App\Policies\Directories\ItuElementPolicy;
use App\Policies\Directories\ItuFaultPolicy;
use App\Policies\Directories\ItuReasonBreakdownPolicy;
use App\Policies\Directories\ItuSpeciePolicy;
use App\Policies\Directories\ObjectInfrastructurePolicy;
use App\Policies\DropVoltage\DropVoltageDevicesPolicy;
use App\Policies\DropVoltage\DropVoltageEventChroniclesPolicy;
use App\Policies\DropVoltage\DropVoltagePolicy;
use App\Policies\Incident\EventChroniclesPolicy;
use App\Policies\Incident\IncidentEmployeeInformationPolicy;
use App\Policies\Incident\IncidentEmployeeReferralPolicy;
use App\Policies\Incident\IncidentPolicy;
use App\Policies\Inspection\InspectionPolicy;
use App\Policies\Inspection\InspectorPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        IncidentType::class => IncidentTypePolicy::class,
        ItuSpecie::class => ItuSpeciePolicy::class,
        ItuCharacteristic::class => ItuCharacteristicPolicy::class,
        ItuElement::class => ItuElementPolicy::class,
        ItuFault::class => ItuFaultPolicy::class,
        ItuDirectoryObject::class => ItuDirectoryObjectPolicy::class,
        ItuReasonBreakdown::class => ItuReasonBreakdownPolicy::class,
        GroupInfrastructureObject::class => GroupInfrastructureObjectPolicy::class,
        Division::class => DivisionPolicy::class,
        ObjectInfrastructure::class => ObjectInfrastructurePolicy::class,
        DispatchArea::class => DispatchAreaPolicy::class,
        User::class => UserPolicy::class,
        Permission::class => PermissionPolicy::class,
        Role::class => RolePolicy::class,
        EventChronicles::class => EventChroniclesPolicy::class,
        IncidentEmployeeReferral::class => IncidentEmployeeReferralPolicy::class,
        IncidentEmployeeInformation::class => IncidentEmployeeInformationPolicy::class,
        Incident::class => IncidentPolicy::class,
        Inspection::class => InspectionPolicy::class,
        DropVoltage::class => DropVoltagePolicy::class,
        DropVoltageEventChronicles::class => DropVoltageEventChroniclesPolicy::class,
        DropVoltageDevices::class => DropVoltageDevicesPolicy::class,
        Inspector::class => InspectorPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('viewLogViewer', function (?User $user) {
            return $user->hasRole('admin');
        });
    }
}
