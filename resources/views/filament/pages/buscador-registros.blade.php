<x-filament-panels::page>
    <form wire:submit="buscar">
        {{ $this->form }}
        <div class="mt-5 flex justify-center">
            <x-filament::button
                type="submit"
                icon="heroicon-o-magnifying-glass"
            >
                Buscar
            </x-filament::button>
        </div>
    </form>
    <div>
        <x-filament::section>
            <x-slot name="heading">
                Resultados de la búsqueda
            </x-slot>
            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>
