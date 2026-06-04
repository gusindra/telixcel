<div>
    <x-jet-section-border />
    <x-jet-action-section>
        <x-slot name="title">
            {{ __('Contract') }}
        </x-slot>

        <x-slot name="description">
            {{ __('List all contract from this project.') }}
        </x-slot>

        <x-slot name="content">
            <livewire:table.contract :project_id="$project->id" searchable="title, status"
                exportable :key="'contract-table-'.$project->id" />
        </x-slot>
    </x-jet-action-section>
</div>
