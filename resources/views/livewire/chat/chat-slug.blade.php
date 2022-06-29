<div>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <div class="hidden md:block round px-2 py-1 mb-3
            {{agentStatus($team->agents)=='Online'?'bg-green-200':''}}
            {{agentStatus($team->agents)=='Away'?'bg-yellow-200':''}}
            {{agentStatus($team->agents)=='Offline'?'bg-gray-200':''}}
            rounded">
                {{agentStatus($team->agents)}}
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
