<div>
    <x-jet-section-border />
    <x-jet-action-section>
        <x-slot name="title">
            {{ __('Order') }}
        </x-slot>

        <x-slot name="description">
            {{ __('List all order from this project.') }}
        </x-slot>

        <x-slot name="content">
            <livewire:table.order :project_id="$project->id" searchable="name, no"
                exportable :key="'order-table-'.$project->id" />
        </x-slot>
    </x-jet-action-section>
</div>
