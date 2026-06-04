<div>
    <x-jet-section-border />
    <x-jet-action-section>
        <x-slot name="title">
            {{ __('Quotation') }}
        </x-slot>

        <x-slot name="description">
            {{ __('List all quotation from this project.') }}
        </x-slot>

        <x-slot name="content">
            <livewire:table.quotation :project_id="$project->id" searchable="title, status"
                exportable :key="'quotation-table-'.$project->id" />
        </x-slot>
    </x-jet-action-section>
</div>
