<div>
    <x-jet-form-section submit="update({{$contract->id}})">
        <x-slot name="title">
            {{ __('1. Contract Basic') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Basic information.') }}
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Contract ID') }}" />
                    <p>{{$contract->id}}</p>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="name" value="{{ __('Title') }}" />
                    <x-jet-input id="name"
                        disabled="{{disableInput($contract->status)}}"
                        type="text"
                        class="mt-1 block w-full"
                        wire:model="input.title"
                        wire:model.defer="input.title"
                        wire:model.debunce.800ms="input.title" />
                    <x-jet-input-error for="name" class="mt-2" />
                </div>
            </div>

            <div class="col-span-6 grid {{$input['model']=='PROJECT'?'grid-cols-2':'grid-cols-1'}}">
                <div class="col-span-12 sm:col-span-1">
                    <div class="col-span-12 sm:col-span-1">
                        <x-jet-label for="input.signer_email" value="{{ __('Email Signers') }}" />
                        <x-jet-input id="input.signer_email"
                            disabled="{{disableInput($contract->status)}}"
                            type="text"
                            class="mt-1 block w-full"
                            wire:model="input.signer_email"
                            wire:model.defer="input.signer_email"
                            wire:model.debunce.800ms="input.signer_email" />
                        <p class='text-xs text-gray-400'>Using comma (,) if more than one</p>
                        <x-jet-input-error for="input.signer_email" class="mt-2" />
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-1">
                    <div class="col-span-12 sm:col-span-1 mx-4">
                        @if($input['model']=="PROJECT")
                            <x-jet-label for="input.signer_email" value="{{ __($contract->project->company->name.' Signer') }}" />
                            <div class="border p-2 border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                                {{$contract->project->company->person_in_charge}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="date" value="{{ __('Start Date') }}" />
                    <x-input.date-picker
                        class="text-sm w-full rounded-md border-gray-300 dark:bg-slate-800" wire:model="input.actived_at" :error="$errors->first('date')"
                        :disabled="disableInput($contract->status)"  />
                    <x-jet-input-error for="date" class="mt-2" />
                </div>
                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="price" value="{{ __('End Date') }}" />
                    <x-input.date-picker
                        class="text-sm w-full rounded-md border-gray-300 dark:bg-slate-800" wire:model="input.expired_at" :error="$errors->first('date')"
                        :disabled="disableInput($contract->status)" />
                    <x-jet-input-error for="price" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Contract saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{in_array($contract->status, ['draft','new'])?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <x-jet-form-section submit="update({{$contract->id}})">
        <x-slot name="title">
            {{ __('2. Sorce Information') }}
        </x-slot>

        <x-slot name="description">
            {{ __('The source information.') }}
        </x-slot>

        <x-slot name="form">
            @if($addressed)
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="input.model" value="{{ __(' ') }}" />
                    <span class="border rounded-md shadow-sm mt-1 block w-full p-2">{{$input['model']}} : {{$client->name}}</span>
                </div>
            </div>
            @endif
            <div class="col-span-6 grid grid-cols-2">
                <div class="col-span-12 sm:col-span-1">
                    <x-jet-label for="input.model" value="{{ __('Source') }}" />
                    <select
                        wire:change="onChangeModel"
                        name="input.model"
                        id="model"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.model"
                        {{disableInput($contract->status)?'disabled':''}}
                        >
                        <option selected>-- Select --</option>
                        <option value="PROJECT">Project</option>
                        <option value="CLIENT">Client</option>

                    </select>
                </div>

                <div class="col-span-12 sm:col-span-1 mx-4">
                    <x-jet-label for="type" value="{{ __('Name') }}" />
                    <select
                        wire:change="onChangeModelId"
                        name="input.model_id"
                        id="model_id"
                        class="border-gray-300 dark:bg-slate-800 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                        wire:model.debunce.800ms="input.model_id"
                        {{disableInput($contract->status)?'disabled':''}}
                        >
                        <option selected>-- Select --</option>
                        @foreach($model_list as $key => $item)
                        <option value="{{$key}}">{{$item}}</option>
                        @endforeach

                    </select>
                    <x-jet-input-error for="status" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Contract saved.') }}
            </x-jet-action-message>

            <x-save-button show="{{in_array($contract->status, ['draft','new'])?true:false}}">
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>

    <x-jet-section-border />

    <div class="md:grid md:grid-cols-5 md:gap-6">
        <div class="md:col-span-1 flex justify-between">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-300">3. File</h3>

                <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
                    Content information
                </p>
            </div>

            <div class="px-4 sm:px-0"> </div>
        </div>

        <div class="mt-0 md:mt-0 md:col-span-4">
            <div class="bg-white dark:bg-slate-600 shadow sm:rounded-md">
                <div class="p-4">

                    @if($contract->status=='approved' || $contract->status=='approve')
                        <table class="table w-full border m-2">
                            <tr>
                                <td>No</td>
                                <td>Uploaded at</td>
                                <td>File</td>
                                <td>Action</td>
                            </tr>
                            @foreach($contract->attachments as $file)
                                <tr>
                                    <td class="border border-gray-300 p-2">{{$loop->iteration}}</td>
                                    <td class="border border-gray-300 p-2">{{$file->created_at->format('d F Y - H:i')}}</td>
                                    <td class="border border-gray-300 p-2"><div><img src="{{url('/backend/img/'.substr(strrchr($file->file, '.'), 1).'.png')}}" class="h-8" title="{{substr(strrchr($file->file, '/'), 1)}}" /></div></td>
                                    <td class="border border-gray-300 p-2"><div><a target="_blank" href="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$file->file}}" title="download"><svg viewBox="0 0 34 34" width="34" height="34" class="ml-2"><path fill="currentColor" d="M17 2c8.3 0 15 6.7 15 15s-6.7 15-15 15S2 25.3 2 17 8.7 2 17 2m0-1C8.2 1 1 8.2 1 17s7.2 16 16 16 16-7.2 16-16S25.8 1 17 1z"></path><path fill="currentColor" d="M22.4 17.5h-3.2v-6.8c0-.4-.3-.7-.7-.7h-3.2c-.4 0-.7.3-.7.7v6.8h-3.2c-.6 0-.8.4-.4.8l5 5.3c.5.7 1 .5 1.5 0l5-5.3c.7-.5.5-.8-.1-.8z"></path></svg></a></div></td>
                                </tr>
                            @endforeach
                        </table>
                    @endif
                    @if ($contract->status=='submit' || $contract->status=='new' || $contract->status=='draft')
                        @livewire('file-upload', ['model'=> 'contract', 'model_id'=>$contract->id, $contract->status])
                    @endif
                </div>
            </div>
        </div>
    </div>


</div>
