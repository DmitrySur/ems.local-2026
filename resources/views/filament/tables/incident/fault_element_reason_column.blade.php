<div class="flex-wrap whitespace-normal text-xs">
@if($getRecord()?->incidentType?->has_faults)
        <ul class="max-w-md space-y-1 ist-inside">
            @if($getRecord()?->ituFault?->title)
                <li class="flex items-center">
                    <x-radix-link-break-2 class="w-4 h-4 text-gray-600" style="margin-right: 5px"/>
                    {{$getRecord()?->ituFault?->title ??''}}
                </li>
            @endif

            @if($getRecord()?->ituElement?->title)
                <li class="flex items-center">
                    <x-radix-frame class="w-4 h-4 text-gray-600" style="margin-right: 5px"/>
                    {{$getRecord()?->ituElement?->title ??''}}
                </li>
            @endif

            @if($getRecord()?->ituReasonBreakdown?->title)
                <li class="flex items-center">
                    <x-radix-thick-arrow-right class="w-4 h-4 text-gray-600" style="margin-right: 5px"/>
                    {{$getRecord()?->ituReasonBreakdown?->title ??''}}
                </li>
            @endif

        </ul>
    @else
    <div>
        {{$getRecord()?->detail_incident}}
    </div>
@endif

</div>

