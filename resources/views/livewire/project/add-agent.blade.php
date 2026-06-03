<div>
    <x-jet-section-border/>

    <div class="md:grid md:grid-cols-5 md:gap-6 mt-8 sm:mt-0">
        <div class="md:col-span-1 flex justify-between">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-300">{{ __('Assigned Users') }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
                    {{ __('Assign existing users to this project.') }}
                </p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-4">
            <div class="px-4 py-5 sm:p-6 bg-white dark:bg-slate-600 shadow sm:rounded-lg">

                {{-- Assign control --}}
                <div class="flex gap-2 items-stretch mb-4">
                    <x-searchable-select field="selectedUser" :options="$availableUsers" sub="email"
                        placeholder="{{ __('Search user...') }}" />
                    <button wire:click="assign" wire:loading.attr="disabled"
                            class="h-10 inline-flex items-center px-5 bg-gray-800 hover:bg-gray-700 text-white text-xs font-semibold uppercase tracking-widest rounded-md transition">
                        {{ __('Assign') }}
                    </button>
                </div>
                <x-jet-input-error for="selectedUser" class="mb-2" />
                <x-jet-action-message class="mb-2 text-sm text-green-600" on="member_assigned">
                    {{ __('Members updated.') }}
                </x-jet-action-message>

                {{-- Assigned list --}}
                <div class="divide-y divide-gray-100 dark:divide-slate-500 border-t border-gray-100 dark:border-slate-500">
                    @forelse ($members as $m)
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center h-9 w-9 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 font-semibold text-sm uppercase">
                                    {{ \Illuminate\Support\Str::substr($m->name, 0, 1) }}
                                </span>
                                <div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-slate-200 capitalize">{{ $m->name }}</div>
                                    <div class="text-xs text-gray-400 dark:text-slate-400">{{ $m->email }}</div>
                                </div>
                            </div>
                            <button wire:click="confirmUnassign({{ $m->id }})"
                                    class="text-xs font-medium text-red-600 hover:text-red-800 border border-red-200 hover:border-red-400 rounded-md px-3 py-1 transition">
                                {{ __('Unassign') }}
                            </button>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-gray-400">
                            {{ __('No users assigned yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Unassign confirmation modal --}}
    <x-jet-confirmation-modal wire:model="confirmingUnassign">
        <x-slot name="title">{{ __('Unassign User') }}</x-slot>
        <x-slot name="content">
            {{ __('Remove') }} <span class="font-semibold">"{{ $unassignName }}"</span> {{ __('from this project?') }}
        </x-slot>
        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingUnassign', false)">{{ __('Cancel') }}</x-jet-secondary-button>
            <x-jet-danger-button class="ml-2" wire:click="unassign">{{ __('Unassign') }}</x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
