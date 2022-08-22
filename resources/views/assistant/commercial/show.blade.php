<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Commerce Item') }}
        </h2>
    </x-slot>
    <header class="bg-white dark:bg-slate-900 dark:border-slate-600 border-b shadow ">
        <div class="flex justify-between pt-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="justify-end flex">
                <div class="items-center justify-end px-2">
                    <div class="space-x-1 sm:-my-px pb-2">
                        <x-jet-nav-link href="{{ route('assistant') }}">
                            {{ __('Assistant') }}
                        </x-jet-nav-link>
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <x-jet-nav-link href="{{ route('commercial') }}">
                            {{ __('Commercial') }}
                        </x-jet-nav-link>
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <x-jet-nav-link href="{{ route('commercial.show', 'product') }}">
                            {{ __('Product') }}
                        </x-jet-nav-link>
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <x-jet-nav-link href="#" :active="true">
                            {{$code}}. {{$data->name}}
                        </x-jet-nav-link>
                    </div>
                </div>
            </div>
            <div class="justify-end flex hidden">
                <div class="items-center justify-end px-2 text-right">
                    <x-jet-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md mb-2">
                                <button type="button" class="inline-flex items-center px-3 py-2 border text-xs leading-4 font-medium rounded-md text-gray-500  bg-gray-200 hover:bg-gray-300 hover:text-gray-700 focus:outline-none focus:bg-gray-400 active:bg-gray-400 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg> Add
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-60">
                                <div>
                                    <form>
                                        <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" href="#" wire:click="updateStatus('Online')">
                                            <div class="flex items-center justify-between">
                                                <div class="truncate">Quotation</div>
                                            </div>
                                        </a>
                                        <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" href="#" wire:click="updateStatus('Online')">
                                            <div class="flex items-center justify-between">
                                                <div class="truncate">Contract</div>
                                            </div>
                                        </a>
                                        <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" href="#" wire:click="updateStatus('Online')">
                                            <div class="flex items-center justify-between">
                                                <div class="truncate">Invoice</div>
                                            </div>
                                        </a>
                                        <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" href="#" wire:click="updateStatus('Online')">
                                            <div class="flex items-center justify-between">
                                                <div class="truncate">Commissioning Agent</div>
                                            </div>
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            </div>
        </div>
    </header>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('commercial.item.edit', ['code'=>$code])
        </div>
    </div>
</x-app-layout>
