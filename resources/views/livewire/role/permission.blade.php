<div>
    <x-jet-section-border />

    <x-jet-form-section submit="updatePermission">
        <x-slot name="title">
            {{ __('Permission') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The Role`s permission.') }}
            <br><br>
        </x-slot>

        <x-slot name="form">

            <div class="col-span-6 sm:col-span-6">
                <span class="text-gray-500 dark:text-slate-300 font-bold">Role Permissions</span>
            </div>

            <!-- is_enabled -->
            <div class="col-span-6 sm:col-span-6">
                <div class="flex1 items-start my-2" wire:poll>

                    @php ($model = '')
                    @foreach( get_permission($role->type, $role->role_for) as $key => $data)
                        @if($model!=$data->model)
                            @if($key!=0)
                                </div>
                            @endif
                            <div class="flex items-center h-5 mb-1">
                                <!-- <input id="status" name="status" wire:model="request.{{$data->model}}" wire:model.defer="request.{{$data->model}}" type="checkbox"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"> -->
                                <span class="text-gray-500 dark:text-slate-300 font-bold">MENU {{$data->model}}</span>
                            </div>
                            <div class="flex col-span-1 sm:col-span-1 space-x-8 lg:space-x-18 mb-8">
                        @endif
                        <div class="flex items-center">
                            <div class="flex items-center h-5">
                                <input id="permission{{$data->id}}" name="permission{{$data->id}}" wire:model="request.{{$data->id}}"
                                    type="checkbox"
                                    value="1"
                                    wire:click.prevent="check({{$data->id}})"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 dark:text-slate-800 border-gray-300 rounded">
                            </div>

                            <div class="ml-3 text-xs">
                                <label for="permission{{$data->id}}" class="text-gray-700 dark:text-slate-300 text-xs">{{$data->name}}</label>
                            </div>
                        </div>
                        @if($loop->last)
                            </div>
                        @endif
                        @php ($model = $data->model)
                    @endforeach
                </div>
            </div>

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Permission Role saved.') }}
            </x-jet-action-message>
        </x-slot>
    </x-jet-form-section>
</div>
