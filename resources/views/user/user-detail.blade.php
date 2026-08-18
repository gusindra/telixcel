<x-app-layout>
    <x-slot name="header">
        <h2 class="capitalize">{{ $user->name }}</h2>
    </x-slot>

    @if($user->id != 0)
        <div class="tx-stack">
            <div class="tx-toolbar">
                <p class="tx-13 tx-fg-muted m-0">{{ __('Tasks owned by or assigned to this user.') }}</p>
                <a href="{{ route('user.show.profile', $user) }}" class="tx-btn tx-btn-ghost">{{ __('Profile') }}</a>
                <a href="{{ route('user.show.balance', $user) }}" class="tx-btn tx-btn-ghost">{{ __('Balance') }}</a>
            </div>

            @livewire('dashboard.dashboard-overview', ['forUserId' => $user->id], key('user-dashboard-'.$user->id))

            <x-page-section :title="__('Projects')">
                <x-slot name="toolbar">
                    <a href="{{ route('project') }}" class="tx-btn tx-btn-ghost">{{ __('View all') }}</a>
                </x-slot>
                <livewire:table.project-table searchable="name" exportable :key="'user-project-table-'.$user->id" />
            </x-page-section>

            <x-page-section :title="__('To-do')">
                @livewire('task.todo', ['ownerId' => $user->id], key('user-todo-'.$user->id))
            </x-page-section>

            <x-page-section :title="__('Teams')">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left tx-fg-muted">
                                <th class="py-2 pr-3 font-medium">{{ __('Name') }}</th>
                                <th class="py-2 px-3 font-medium">{{ __('Members') }}</th>
                                <th class="py-2 px-3 font-medium">{{ __('Created') }}</th>
                                <th class="py-2 pl-3 font-medium">{{ __('Slug') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($user->teams as $team)
                                <tr style="box-shadow: inset 0 -1px 0 var(--tx-border);">
                                    <td class="py-2.5 pr-3 font-medium tx-fg">{{ $team->name }}</td>
                                    <td class="py-2.5 px-3">{{ $team->users_count ?? $team->users()->count() }}</td>
                                    <td class="py-2.5 px-3">{{ $team->created_at->format('d M Y') }}</td>
                                    <td class="py-2.5 pl-3">
                                        <a class="tx-row-link" href="{{ url('chatting', $team->slug) }}">{{ $team->slug }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center tx-fg-muted">{{ __('No team.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-page-section>
        </div>
    @endif
</x-app-layout>
