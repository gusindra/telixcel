<x-jet-form-section submit="updateChatTeam">
    <x-slot name="title">
        {{ __('Team Chat') }}
    </x-slot>

    <x-slot name="description">
        {{ __('The team\'s chat to testing and connect with your client.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Team Chat Slug -->
        <div class="col-span-6 sm:col-span-5">
            <x-jet-label for="slug" value="{{ __('Team Slug URL') }}" />
            <div >
                <div class="mt-1 flex rounded-md shadow-sm">
                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 dark:bg-slate-800 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        {{url('/')}}/chating/
                    </span>
                    <!-- <input type="text" name="slug" id="slug" class="focus:ring-indigo-500 dark:bg-slate-800 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300" placeholder="{{$team->name}}"
                        wire:model.defer="state.slug"
                        :disabled="! Gate::check('update', $team)"
                    > -->
                    <x-jet-input id="slug" type="text" class="rounded-l-none" wire:model="slug" wire:model.defer="slug" wire:model.debunce.800ms="slug" />

                    <a target="blank" href="{{route('chat.slug', $slug)}}" class="flex items-center justify-center ml-2 px-4 py-2 rounded-md shadow-sm text-sm font-medium text-gray-200 bg-green-600 hover:bg-green-500">
                        Testing Message Now
                    </a>
                </div>
            </div>


            <x-jet-input-error for="name" class="mt-2" />
        </div>
        <!-- Chat Embed Script -->
        <div class="col-span-6 sm:col-span-5">
            <x-jet-label for="slug" value="{{ __('Embed Script to Website') }}" />
            <div>
                <div class="mt-1 flex rounded-md shadow-sm">
                    <textarea class="dark:text-slate-300 dark:bg-slate-700 w-full h-40" readonly="true"><!--Start of Telixcel Chat Script-->
<div style="position: fixed;bottom: 0;padding: 10px;z-index: 99;text-align: right;"><div id="frame" data-id="{{$dataId}}" style="position: fixed;bottom: -15px;width: auto;right: 10px;"></div></div>
<script src="https://telixcel.s3.ap-southeast-1.amazonaws.com/assets/telixcel-chat.min.js" type="text/javascript"></script>
<!--End of Telixcel Chat Script--></textarea>
                </div>
                <small class="dark:text-slate-400">put this script inside the body</small>
            </div>

            <x-jet-input-error for="name" class="mt-2" />
        </div>
    </x-slot>
    @if (Gate::check('update', $team))
        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    @endif
</x-jet-form-section>
