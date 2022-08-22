<div>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <div class="flex">
                <div class="hidden md:block round px-2 py-1 mb-3
                {{agentStatus($team->agents)=='Online'?'bg-green-200 dark:bg-transparent border dark:border-white/40':''}}
                {{agentStatus($team->agents)=='Away'?'bg-yellow-200 dark:bg-transparent border dark:border-white/40':''}}
                {{agentStatus($team->agents)=='Offline'?'bg-gray-200 dark:bg-transparent border dark:border-white/40':''}}
                rounded">
                    {{agentStatus($team->agents)}}
                </div>
                <button x-cloak x-on:click="darkMode==='true' || darkMode==true ? darkMode=false : darkMode=true;" class="px-4 py-1 mb-4">
                    <!-- Icon Moon -->
                    <svg x-show="darkMode==false||darkMode==='false'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <!-- Icon Sun -->
                    <svg x-show="darkMode==true||darkMode==='true'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
            </div>
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        @if(!$client)
            <div class="p-6">
                <div>
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input
                    wire:model="name" wire:model.defer="name"
                    id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"  required autofocus />
                </div>

                <div class="mt-4">
                    <x-jet-label for="phone" value="{{ __('Phone Number') }}" />
                    <x-jet-input
                    wire:model="number" wire:model.defer="number"
                    id="phone" class="block mt-1 w-full" type="text" name="phone" required autocomplete="current-phone" />
                </div>

                <div class="items-center py-3 content-center">
                    <x-jet-button wire:click="checkClient" class="px-4 py-3">
                        {{ __('Message') }}
                    </x-jet-button>
                </div>
            </div>
        @else
            @livewire('chat.chat-box', ['client_id' => $client->id, 'team' => $team])
        @endif
    </x-jet-authentication-card>

</div>
