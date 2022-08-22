<x-app-layout>
    <header class="bg-white dark:bg-slate-900 dark:border-slate-600 border-b shadow">
        <div class="flex justify-between pt-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="justify-end flex">
                <div class="items-center justify-end px-2">
                    <div class="space-x-1 sm:-my-px pb-3">
                        <x-jet-nav-link href="{{ route('role.index') }}">
                            {{ __('Role') }}
                        </x-jet-nav-link>
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>

                        <x-jet-nav-link href="#" :active="true">
                            {{$role->name}}
                        </x-jet-nav-link>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('role.edit', ['uuid'=>$role->id])
        </div>
    </div>
</x-app-layout>
