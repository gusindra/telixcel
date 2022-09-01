<div>
    <div class="w-full">
        <div class="container max-w-4xl mx-auto">
            @if (session()->has('message'))
            <div class="flex items-center px-4 py-3 mb-6 text-sm font-bold text-white bg-green-500 rounded" role="alert">
                <svg class="w-4 h-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" />
                </svg>
                <p>{{ session('message') }}</p>
            </div>
            @endif
            <x-jet-action-message class="mr-3" on="deleted">
                {{ __('1 selected document deleted.') }}
            </x-jet-action-message>
            <div class="flex flex-wrap1 -mx-2">
                <table class="table w-full border m-2">
                    <tr class="border-b">
                        <td class="border text-center">No</td>
                        <td class="border text-center">Uploaded at</td>
                        <td class="border text-center">File</td>
                        <td class="border text-center">Action</td>
                    </tr>
                    @forelse($files as $file)
                        <tr>
                            <td class="border border-gray-300 p-2">{{$loop->iteration}}</td>
                            <td class="border border-gray-300 p-2">{{$file->created_at->format('d F Y - H:i')}}</td>
                            <td class="border border-gray-300 p-2"><div><img src="{{url('/backend/img/'.substr(strrchr($file->file, '.'), 1).'.png')}}" class="h-8" title="{{substr(strrchr($file->file, '/'), 1)}}" /></div></td>
                            <td class="border border-gray-300 p-2">
                                <div class="flex justify-around">
                                    <a target="_blank" href="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$file->file}}" title="download"><svg viewBox="0 0 34 34" class="w-6 h-6 ml-2"><path fill="currentColor" d="M17 2c8.3 0 15 6.7 15 15s-6.7 15-15 15S2 25.3 2 17 8.7 2 17 2m0-1C8.2 1 1 8.2 1 17s7.2 16 16 16 16-7.2 16-16S25.8 1 17 1z"></path><path fill="currentColor" d="M22.4 17.5h-3.2v-6.8c0-.4-.3-.7-.7-.7h-3.2c-.4 0-.7.3-.7.7v6.8h-3.2c-.6 0-.8.4-.4.8l5 5.3c.5.7 1 .5 1.5 0l5-5.3c.7-.5.5-.8-.1-.8z"></path></svg></a>
                                    @if(!disableInput($status))
                                    <a wire:click="actionConfirmation({{$file->id}})" class="cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"> <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /> </svg></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td>No Files Uploaded</td></tr>
                    @endforelse
                </table>
            </div>

            @if($uploadAble)
            <div class="mb-0 pb-6">
                <form wire:submit.prevent="save" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input class="dark:text-slate-300" type="file" wire:model="input_file">
                        <div>
                            @error('input_file') <span class="text-sm italic text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div wire:loading wire:target="input_file" class="text-sm italic text-gray-500">Uploading...</div>
                    </div>
                    <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">Upload File</button>
                </form>
            </div>
            @endif

        </div>
        <!-- Form Action Modal -->
        <x-jet-dialog-modal wire:model="deleteConfirmation">
            <x-slot name="title">
                {{ __('Delete File Attachment') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to delete this Document?') }}
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('deleteConfirmation')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-jet-secondary-button>

                <x-jet-danger-button class="ml-2" wire:click="remove" wire:loading.attr="disabled">
                    {{ __('Delete File') }}
                </x-jet-danger-button>
            </x-slot>
        </x-jet-dialog-modal>
    </div>
</div>
