<div class="flex items-center">
    @if($getRecord()->status_resolution === \App\Enum\IncidentStatuses::InRepair->value)
        <span style="--c-50:var(--warning-50);--c-400:var(--warning-400);--c-600:var(--warning-600);"
              class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-sm font-medium ring-1 ring-inset px-1 min-w-[theme(spacing.6)] py-1 fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10 dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30">
        @else
                <span
                    class="fi-badge flex items-center justify-center gap-x-1 rounded-md text-sm font-medium ring-1 ring-inset px-1 min-w-[theme(spacing.6)] py-1 fi-color-gray bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20">
        @endif
    <span class="grid">
        <span class="flex">
            <x-radix-calendar class="w-5 h-5" style="margin-right: 3px"/>
            {{$getRecord()->datetime_incident->format('d.m.y')}}
            <x-radix-clock class="w-5 h-5" style="margin-right: 3px; margin-left: 3px"/>
            {{$getRecord()->datetime_incident->format('H:i')}}
        </span>
        </span>

</span>
                @if($getRecord()->incident_classification === 'ННР')
                    <x-iconsax-bol-danger class="w-4 h-4" style="color: #CC3333; margin-left: 5px"/>
    @endif


</div>
