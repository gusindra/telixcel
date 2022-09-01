<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contract Detail') }}
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

                        @if(app('request')->input('source')=='project')
                            <x-jet-nav-link href="{{ route('project.show', app('request')->input('id')) }}">
                                {{ __('Project ') }} {{$contract->project ? ': '.$contract->project->name : ''}}
                            </x-jet-nav-link>
                            <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <x-jet-nav-link href="#" :active="true">
                                Contract : {{$contract->title}}
                            </x-jet-nav-link>
                        @else
                            <x-jet-nav-link href="{{ route('commercial') }}">
                                {{ __('Commercial') }}
                            </x-jet-nav-link>
                            <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <x-jet-nav-link href="{{ route('commercial.show', 'contract ') }}">
                                {{ __('Contract ') }}
                            </x-jet-nav-link>
                            <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <x-jet-nav-link href="#" :active="true">
                                {{$code}}. {{$contract->title}}
                            </x-jet-nav-link>
                        @endif
                    </div>
                </div>
            </div>
            @if($contract->status=='approved')
            <div class="justify-end flex">
                <div class="items-center justify-end px-2 text-right">
                    <x-jet-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md mb-2">
                                <button type="button" class="inline-flex items-center px-3 py-2 border text-xs leading-4 font-medium rounded-md text-gray-500  bg-gray-200 hover:bg-gray-300 hover:text-gray-700 focus:outline-none focus:bg-gray-400 active:bg-gray-400 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-60">
                                <div>
                                    <form>
                                        <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" target="_blank" href="{{route('commercial.print', ['type'=>'contract','id'=>$code])}}">
                                            <div class="flex items-center justify-between">
                                                <div class="truncate">Download</div>
                                            </div>
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            </div>
            @endif
        </div>
    </header>

    <div>
        <div class="max-w-7xl mx-auto pt-4 pb-10 sm:px-6 lg:px-8">
            <div class="{{$contract->status=='approved'?'bg-green-100':'bg-gray-200'}} border sm:rounded {{$contract->status=='approved'?'border-green-500':'border-gray-500'}} {{$contract->status=='approved'?'text-green-700':'text-gray-700'}} px-4 py-3 mb-4" role="alert">
                <p class="font-bold capitalize">{{$contract->status}}</p>
                @if($contract->status=='draft' && is_null($contract->original_attachment))
                <p>Please upload final draft contract..!</p>
                @elseif($contract->status=='submit' && is_null($contract->result_attachment))
                <p>Please upload signing final contract..!</p>
                @endif
            </div>
            <div class="md:grid md:grid-cols-5 md:gap-6 mt-8 sm:mt-0">
                <div class="md:col-span-4">
                    @livewire('commercial.contract.edit', ['code'=>$code])
                </div>
                <div class="col-span-1">
                    <div class="justify-between lg:visible md:invisible">
                        @livewire('commercial.progress', ['model'=>'contract','id'=>$code])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
