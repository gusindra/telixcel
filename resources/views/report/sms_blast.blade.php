<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('SMS Request') }}
        </h2>
    </x-slot>

    @include('report.nav')

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-2 py-4">
            <div class="sm:px-6 lg:px-6">
                <h3 class="font-semibold text-gray-800 dark:text-slate-300 leading-tight pb-2">
                    {{ __('SMS Blast Table') }}
                </h3>
                <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                    <livewire:table.sms-blast-table searchable="{{auth()->user()->super->first()->role == 'superadmin' ? 'user_id, status, created_at':'status, msisdn, created_at, message_content'" exportable />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
