<x-filament-panels::page>
    <form wire:submit="validar">
        {{ $this->form }}
        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" wire:loading.attr="disabled" wire:target="validar">
                <span wire:loading.remove wire:target="validar" class="flex items-center gap-x-2">
                    <x-filament::icon icon="heroicon-o-check" class="h-5 w-5" />
                    Validar archivo
                </span>
                <span wire:loading.flex wire:target="validar" class="items-center gap-x-2">
                    Validando archivo...
                </span>
            </x-filament::button>
        </div>
    </form>

    @if ($this->importacionId)
        @php
            $resumen = $this->resumenValidacion();
        @endphp
        <div class="mt-8 space-y-6">
            <x-filament::section>
                <x-slot name="heading">
                    Resumen de validación
                </x-slot>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Total
                        </div>
                        <div class="mt-1 text-2xl font-bold">
                            {{ number_format($resumen['total']) }}
                        </div>
                    </div>
                    <div class="rounded-xl bg-success-50 p-4 dark:bg-success-950">
                        <div class="text-sm font-medium text-success-700 dark:text-success-400">
                            Válidos
                        </div>
                        <div class="mt-1 text-2xl font-bold text-success-700 dark:text-success-400">
                            {{ number_format($resumen['validos']) }}
                        </div>
                    </div>
                    <div class="rounded-xl bg-warning-50 p-4 dark:bg-warning-950">
                        <div class="text-sm font-medium text-warning-700 dark:text-warning-400">
                            Existentes
                        </div>
                        <div class="mt-1 text-2xl font-bold text-warning-700 dark:text-warning-400">
                            {{ number_format($resumen['existentes']) }}
                        </div>
                    </div>
                    <div class="rounded-xl bg-danger-50 p-4 dark:bg-danger-950">
                        <div class="text-sm font-medium text-danger-700 dark:text-danger-400">
                            Errores
                        </div>
                        <div class="mt-1 text-2xl font-bold text-danger-700 dark:text-danger-400">
                            {{ number_format($resumen['errores']) }}
                        </div>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Registros encontrados
                </x-slot>
                <x-slot name="description">
                    Revise los registros antes de guardarlos definitivamente.
                </x-slot>
                {{ $this->table }}
            </x-filament::section>

            @if ($this->puedeGuardar())
                <div class="flex justify-end">
                    <x-filament::button
                        wire:click="mountAction('guardarRegistros')"
                        icon="heroicon-o-arrow-down-tray"
                        color="success"
                    >
                        Guardar registros seleccionados
                    </x-filament::button>
                </div>
            @endif

        </div>
    @endif

</x-filament-panels::page>
