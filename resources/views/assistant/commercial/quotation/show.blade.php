<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quotation Detail') }}
        </h2>
    </x-slot>
    <header class="bg-white dark:bg-slate-900 border-b dark:border-slate-600 shadow">
        <div class="flex justify-between pt-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="justify-end flex">
                <div class="items-center justify-end px-2">
                    <div class="space-x-1 sm:-my-px pb-2">
                        <x-jet-nav-link href="{{ route('assistant') }}">
                            {{ __('Assistant') }}
                        </x-jet-nav-link>
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 dark:text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        @if(app('request')->input('source')=='project' || $quote->model=='PROJECT')
                            <x-jet-nav-link href="{{ route('project.show', app('request')->input('id') ?? $quote->model_id) }}">
                                {{ __('Project ') }} {{$quote->project ? ': '.$quote->project->name : ''}}
                            </x-jet-nav-link>
                        @else
                            <x-jet-nav-link href="{{ route('commercial') }}">
                                {{ __('Commercial') }}
                            </x-jet-nav-link>
                            <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 dark:text-slate-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <x-jet-nav-link href="{{ route('commercial.show', 'quotation') }}">
                                {{ __('Quotation ') }}
                            </x-jet-nav-link>
                        @endif
                        <span class="inline-flex items-center px-1 pt-1 text-xs font-medium leading-5 text-gray-900 dark:text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <x-jet-nav-link href="#" :active="true">
                            {{$code}}. {{$quote->title}}
                        </x-jet-nav-link>
                    </div>
                </div>
            </div>

            @if($quote->status=='approved' || $quote->status=='released' || $quote->status=='reviewed')
                <div class="justify-end flex">
                    @if(!$quote->order)
                        @can('create', App\Models\Order::class)
                            @livewire('order.quotation-to-order', ['id'=>$code])
                        @endcan
                    @else
                        <span class="inline-flex rounded-md mb-2">
                            <a href="{{route('show.order', $quote->order->id)}}" class="inline-flex dark:bg-slate-800 dark:text-slate-300 items-center px-2 py-1 border text-xs leading-4 font-medium rounded-md text-gray-500  bg-gray-200 hover:bg-gray-300 hover:text-gray-700 focus:outline-none focus:bg-gray-400 active:bg-gray-400 transition">
                                View Order
                            </a>
                        </span>
                    @endif
                    <div class="items-center justify-end px-2 text-right">
                        <x-jet-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md mb-2">
                                    <button type="button" class="inline-flex dark:bg-slate-800 dark:text-slate-300 items-center px-3 py-2 border text-xs leading-4 font-medium rounded-md text-gray-500  bg-gray-200 hover:bg-gray-300 hover:text-gray-700 focus:outline-none focus:bg-gray-400 active:bg-gray-400 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <div>
                                        @if (count($quote->attachments)>0)
                                            @foreach($quote->attachments as $file)
                                                <a title="Quotation upload at {{$file->created_at->format('d F Y - H:i')}}" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" target="_blank" href="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$file->file}}">
                                                    <div class="flex items-center justify-between">
                                                        <div class="truncate text-xs">Download Quotation {{$loop->iteration}}</div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        @else
                                            <a class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" target="_blank" href="{{route('commercial.print', ['type'=>'quotation','id'=>$code])}}">
                                                <div class="flex items-center justify-between">
                                                    <div class="truncate">Print</div>
                                                </div>
                                            </a>
                                        @endif
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
            @if($quote->status=='approved' || $quote->status=='released')
                <div class="md:grid md:grid-cols-5 md:gap-4">
                    <div class="md:col-span-1 flex justify-between">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-slate-300">File Upload</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-slate-300">
                                File information
                            </p>
                        </div>
                    </div>

                    <div class="mt-0 md:mt-0 md:col-span-4">
                        <div class="bg-white dark:bg-slate-600 shadow sm:rounded-md">
                            <div class="pt-4">
                                @livewire('file-upload', ['model'=> 'quotation', 'model_id'=>$code, 'status'=>$quote->status])
                            </div>
                        </div>
                    </div>
                </div>
                <x-jet-section-border />
            @endif
            <div class="md:grid md:grid-cols-5 md:gap-6 mt-8 sm:mt-0">
                <div class="md:col-span-4">
                    @livewire('commercial.quotation.edit', ['code'=>$code, 'source'=>app('request')->input('source'), 'source_id'=>app('request')->input('id')])
                </div>
                <div class="col-span-1">
                    <div class="justify-between lg:visible md:invisible">
                        @livewire('commercial.progress', ['model'=>'quotation','id'=>$code])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
