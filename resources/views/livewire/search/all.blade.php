<div>
    <div class="flex items-center p-4 text-right">
        <a wire:click="actionShowModal" class="inline-flex dark:hover:bg-slate-600 cursor-pointer items-center px-2 py-1 text-gray-600 dark:bg-slate-800 border border-transparent rounded-md font-normal text-xs dark:text-white 1g-widest hover:text-slate-700 active:bg-green-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </a>
    </div>

    <!-- Form Action Modal -->
    <x-jet-dialog-modal wire:model="modalActionVisible">
        <x-slot name="title">
            {{ __('Search') }}
        </x-slot>

        <x-slot name="content">

            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-input wire:keydown.enter="search" placeholder="Press enter to search" id="keyword" type="text" class="mt-1 block w-full" wire:model.debunce.800ms="keyword" autofocus />
                <x-jet-input-error for="keyword" class="mt-2" />
                <p class="text-right text-xs p-2 text-slate-400">Press Enter to Search</p>
            </div>
            <div class="text-center">
                <p wire:loading>Loading...</p>
            </div>
            <div>
                <div class="z-30">
                    @foreach ($results as $key => $model)
                        <div class="border-b border-gray-300 pb-2">
                            <div class="block px-3 text-gray-400 transition-colors duration-200 focus:outline-none focus:bg-dark-800 " >
                                <div  class="text-sm font-medium"></div>
                                <div class="mt-2">
                                    <div class="font-bold">
                                        <span class="text-red-600 opacity-75">#</span> <span>{{$key}}</span>
                                    </div>

                                    @foreach ($model as $data)
                                    <div class="focus:text-gray-200 hover:text-gray-200" @keydown.arrow-up.prevent="focusPreviousResult(index)" @keydown.arrow-down.prevent="focusNextResult(index)">
                                        &gt;
                                        <a href="{{$data['url']}}">
                                            @foreach ($data['fields_formatted'] as $k => $res)
                                                <span>{{$res}} : {{@$data[$k]}}</span>
                                            @endforeach
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8 pb-32" style="display: none;">
                    <div>We didn't find any result for '12'. Sorry!</div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalActionVisible')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <!-- <x-jet-button class="ml-2" wire:click="search" wire:loading.attr="disabled">
                {{ __('Search') }}
            </x-jet-button> -->
        </x-slot>
    </x-jet-dialog-modal>
</div>

