<div class="tx-stack">
    <x-page-section :title="__('Projects')">
        <x-slot name="toolbar">
            <a href="{{ route('project') }}" class="tx-btn tx-btn-ghost">{{ __('View all') }}</a>
        </x-slot>
        <livewire:table.project-table searchable="name" exportable :key="'dash-project-table'" />
    </x-page-section>

    <x-page-section :title="__('Tasks')">
        @livewire('task.todo', ['ownerId' => $ownerId ?? null], key('dash-todo-'.($ownerId ?? 'all')))
    </x-page-section>
</div>
