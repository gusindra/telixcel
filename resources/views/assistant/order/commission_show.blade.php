<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Commission') }}
        </h2>
    </x-slot>
    <header class="bg-white dark:bg-slate-900 dark:border-slate-600 border-b shadow">
        <div class="flex justify-between pt-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="justify-end flex mb-2">
                <div class="items-center justify-end px-2">
                    <div class="space-x-1 sm:-my-px">
                        <x-jet-nav-link href="{{ route('assistant') }}">
                            {{ __('Assistant') }}
                        </x-jet-nav-link>
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <x-jet-nav-link href="{{ route('commission') }}">
                            {{ __('Commission ') }}
                        </x-jet-nav-link>
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <x-jet-nav-link href="#" :active="true">
                            {{$data->id}}
                        </x-jet-nav-link>
                    </div>
                </div>
            </div>


        </div>
    </header>
    <div>
        <div class="max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 mb-6">
            @if($data->status=='paid')
            <div class="bg-green-100 border sm:rounde border-green-500 text-green-700 px-4 py-3 mb-4" role="alert">
                <p class="font-bold capitalize">{{$data->status}}</p>
            </div>
            @endif
            <div class="md:grid md:grid-cols-5 md:gap-6">
                <div class="md:col-span-12 lg:col-span-4">
                    @livewire('commission.approval', ['uuid'=>$data->id])
                </div>
                <div class="justify-between lg:visible md:invisible">
                    @livewire('commercial.progress', ['model'=>'commission','id'=>$data->id])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
