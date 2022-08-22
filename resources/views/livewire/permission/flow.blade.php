<div>
    <x-jet-form-section submit="addFlow">
        <x-slot name="title">
            {{ __('Flow') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Basic flow for ') }} <span class="capitalize">{{$model}}</span>
        </x-slot>

        <x-slot name="form">
            <div class="col-span-12 sm:col-span-12">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Add Task Approval') }}" />
                </div>
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-jet-label for="input.role_id" value="{{ __('Role') }}" />
                <select
                    name="input.role_id"
                    id="input.role_id"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="input.role_id"
                    >
                    <option selected>-- Select Role --</option>
                    @foreach ($role as $r)
                        <option value="{{$r->id}}">{{@$r->name}}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="input.role_id" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-jet-label for="input.team_id" value="{{ __('Team') }}" />
                <select
                    name="input.team_id"
                    id="input.team_id"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="input.team_id"
                    >
                    <option selected>-- Select Team --</option>
                    @foreach ($team as $t)
                        <option value="{{$t->id}}">{{@$t->name}}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="input.team_id" class="mt-2" />
            </div>
            <div class="col-span-6 sm:col-span-12">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="input.description" value="{{ __('Create Task for') }}" />
                    <x-jet-input id="input.description" type="text" class="mt-1 block w-full" wire:model="input.description" wire:model.defer="input.description" wire:model.debunce.800ms="input.description" />
                    <x-jet-input-error for="input.description" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-jet-label for="input.after_status" value="{{ __('Task for Status') }}" />
                <select
                    name="input.after_status"
                    id="input.after_status"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="input.after_status"
                    >
                    <option selected>-- Select Status --</option>
                    @foreach ($status as $s)
                        <option value="{{$s}}">{{$s}}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="input.after_status" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-jet-label for="input.status" value="{{ __('Task for Status') }}" />
                <select
                    name="input.status"
                    id="input.status"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="input.status"
                    >
                    <option selected>-- Select Status --</option>
                    @foreach ($status as $s)
                        <option value="{{$s}}">{{$s}}</option>
                    @endforeach
                </select>
                <x-jet-input-error for="input.status" class="mt-2" />
            </div>



            <!-- <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="input.user_id" value="{{ __('Spesific Member') }}" />
                <select
                    name="input.user_id"
                    id="input.user_id"
                    class="border-gray-300 dark:bg-slate-800 dark:text-slate-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                    wire:model.debunce.800ms="input.user_id"
                    >
                    <option selected>-- Select --</option>
                </select>
                <x-jet-input-error for="input.user_id" class="mt-2" />
            </div> -->

        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Flow added.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Add') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="#">
        <x-slot name="title">
            {{ __('Flow') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Basic information.') }} <span class="capitalize">{{$model}}</span>
        </x-slot>

        <x-slot name="form">

            <table class="table-auto text-xs sm:text-sm col-span-12">
                <thead>
                    <tr>
                        <th class="px-2 py-2 border border-light-blue-500">No</th>
                        <th class="px-2 py-2 border border-light-blue-500 w-auto">From Status</th>
                        <th class="px-2 py-2 border border-light-blue-500 w-auto">To Status</th>
                        <th class="px-2 py-2 border border-light-blue-500">Description</th>
                        <th class="px-2 py-2 border border-light-blue-500 w-auto">Role</th>
                        <th class="px-2 py-2 border border-light-blue-500">Team</th>
                        <th class="px-2 py-2 border border-light-blue-500">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr>
                            <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium text-center">{{$loop->iteration}} </td>
                            <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-1/5">{{$d->after_status}} </td>
                            <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-1/5">{{$d->result_status}} </td>
                            <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-3/4">{{$d->description}} </td>
                            <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-1/4">{{$d->role ? $d->role->name : '-'}}</td>
                            <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">{{$d->team ? $d->team->name : '-'}} </td>
                            <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">
                                <a href="#" class="text-red-400 px-2 underline" wire:click="deleteFlow({{$d->id}})">Delete</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="deleted">
                {{ __('Flow deleted.') }}
            </x-jet-action-message>
        </x-slot>
    </x-jet-form-section>
</div>
