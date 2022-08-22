<div>
    <div>
        @if ($data->count())
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Name</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Data</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-700 divide-y divide-gray-200">
                    @foreach ($data as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                {{ $item->name }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                {{ $item->value }}
                            </td>
                            <td class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                                <div class="flex items-center">
                                    <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="delete('{{ $item->id }}')">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        @endif
        @if ($array)
        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Name</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Data</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-700 divide-y divide-gray-200">
                    @foreach ($array as $key => $item)
                        <tr>
                            <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                {{ '{'.$item['name'].'}' }}
                            </td>
                            <td class="px-6 py-4 text-sm whitespace-no-wrap">
                                {{ $item['value'] }}
                            </td>
                            <td class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
                                <div class="flex items-center">
                                    <button class="cursor-pointer ml-6 text-sm text-red-500" wire:click="remove('{{ $key }}')">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
        @endif
        <br>
    </div>
    <div class="flex gap-4">
        <div class="flex-none">
            <x-jet-input-error for="name" class="mt-2" />
            <div class="flex rounded-md shadow-sm">
                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    {
                </span>
                <input type="text" name="name" id="name" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none sm:text-sm border-gray-300"
                    placeholder="Blade Name"
                    wire:model="name"
                    wire:model.defer="name"
                    wire:model.debunce.800ms="name"
                >
                <span class="inline-flex items-center px-3 rounded-r-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    }
                </span>
            </div>
        </div>
        <div class="flex-none">
            <input type="text" name="value" id="value" class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none sm:text-sm border-gray-300"
                placeholder="[result][name]"
                wire:model="value"
                wire:model.defer="value"
                wire:model.debunce.800ms="value"
            >
            <x-jet-input-error for="value" class="mt-2" />
        </div>
        <div class="flex-none">
        @if($actionId)
            <x-jet-secondary-button class="ml-2" wire:click="create">
                {{ __('Insert') }}
            </x-jet-secondary-button>
        @else
            <x-jet-secondary-button class="ml-2" wire:click="insert">
                {{ __('Insert') }}
            </x-jet-secondary-button>
        @endif
        </div>
    </div>
    <br>

</div>
