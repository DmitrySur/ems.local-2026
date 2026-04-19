<x-filament::dropdown>
    <x-slot name="trigger">
        <a href="#">Добавить сведения</a>
    </x-slot>

    <x-filament::dropdown.list>
        @foreach($my_list as $item)
            <x-filament::dropdown.list.item wire:click="$set('{{$my_component->getStatePath()}}', '{{$item}}')">
                {{$item}}
            </x-filament::dropdown.list.item>
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown>
