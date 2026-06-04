<x-app-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
        <div class="mb-5">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('Ticket') }}</h1>
            <p class="text-sm text-gray-400 dark:text-slate-400">{{ __('Requests / issues. Turn any ticket into a to-do.') }}</p>
        </div>

        <div class="bg-white dark:bg-slate-700 shadow rounded-lg p-4 sm:p-6">
            @livewire('ticket.board')
        </div>
    </div>
</x-app-layout>
