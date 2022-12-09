@extends('layouts.side-menu')

@section('title', 'Projects')

@section('sidebar')
@parent

<p>This is appended to the master sidebar.</p>
@endsection

@section('content')
<nav class="absolute z-20 flex flex-wrap items-center justify-between w-full px-6 py-2 text-white transition-all shadow-none duration-250 ease-soft-in lg:flex-nowrap lg:justify-start" navbar-profile navbar-scroll="true">
        <div class="flex items-center justify-between w-full px-6 py-1 mx-auto flex-wrap-inherit">
            <nav>
                <ol class="flex flex-wrap pt-1 pl-2 pr-4 mr-12 bg-transparent rounded-lg sm:mr-16">
                    <li class="text-sm pl-2 capitalize leading-normal before:float-left before:pr-2 before:content-['/']" aria-current="page">My Project</li>
                </ol>
                <h6 class="mb-2 ml-2 font-bold text-white capitalize">List</h6>
            </nav>
            <div class="flex items-center">
                <a mini-sidenav-burger href="javascript:;" class="hidden p-0 text-white transition-all ease-nav-brand text-sm xl:block" aria-expanded="false">
                    <div class="w-4.5 overflow-hidden">
                        <i class="ease-soft mb-0.75 relative block h-0.5 translate-x-[5px] rounded-sm bg-white transition-all dark:bg-white"></i>
                        <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all dark:bg-white"></i>
                        <i class="ease-soft relative block h-0.5 translate-x-[5px] rounded-sm bg-white transition-all dark:bg-white"></i>
                    </div>
                </a>
            </div>
            <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
                <div class="flex items-center md:ml-auto md:pr-4">
                    <div class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease-soft">
                        @livewire('search.all')
                    </div>
                </div>
                <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
                    <li class="flex items-center pl-4 xl:hidden">
                        <a href="javascript:;" class="block p-0 text-white transition-all ease-soft-in-out text-sm" sidenav-trigger>
                            <div class="w-4.5 overflow-hidden">
                                <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                                <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-white transition-all"></i>
                                <i class="ease-soft relative block h-0.5 rounded-sm bg-white transition-all"></i>
                            </div>
                        </a>
                    </li>
                    <li class="flex items-center px-4">
                        <a href="javascript:;" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white supports-backdrop-blur:bg-white/60 dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-600 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                            <i fixed-plugin-button-nav class="cursor-pointer fa fa-cog dark:text-white text-slate-700" aria-hidden="true"></i>
                        </a>
                    </li>

                    <li class="relative flex items-center pr-2">
                        <p class="hidden transform-dropdown-show"></p>
                        @livewire('notification-app', ['client_id' => Auth::user()->id], key(Auth::user()->id))
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="w-full px-6 mx-auto">
        <div class="min-h-75 relative mt-0 flex items-center overflow-hidden rounded-sm bg-cover bg-center p-0">
            <span class="absolute inset-y-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-purple-700 to-slate-800 opacity-60"></span>
        </div>
        <div class="relative flex flex-col flex-auto min-w-0 p-4 mx-6 -mt-16 overflow-hidden break-words border-0 shadow-blur dark:shadow-soft-dark-xl dark:bg-gray-950 rounded-2xl bg-white/80 bg-clip-border backdrop-blur-2xl backdrop-saturate-200">
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-auto max-w-full px-3">
                    <div class="text-base ease-soft-in-out h-19 w-19 relative inline-flex items-center justify-center rounded-xl text-white transition-all duration-200">
                        <img src="{{auth()->user()->profile_photo_url?auth()->user()->profile_photo_url:'https://ui-avatars.com/api/?name='.auth()->user()->name.'&amp;color=7F9CF5&amp;background=EBF4FF'}}" alt="{{auth()->user()->name}}" class="w-full shadow-soft-sm rounded-xl">
                    </div>
                </div>
                <div class="flex-none w-auto max-w-full px-3 my-auto">
                    <div class="h-full">
                        <h5 class="mb-1 dark:text-white">{{auth()->user()->company->name}}</h5>
                        <p class="mb-0 font-semibold leading-normal text-sm dark:text-white dark:opacity-60">{{auth()->user()->name}}</p>
                    </div>
                </div>
                <div class="w-full max-w-full px-3 mx-auto mt-4 sm:my-auto sm:mr-0 md:w-1/2 md:flex-none lg:w-4/12">
                    <div class="relative right-0">
                        <ul class="relative flex p-1 list-none bg-transparent rounded-xl" role="list">
                            <li class="z-30 flex-auto text-center">
                                <a class="{{app('request')->input ('display') == 'table'?'':'dark:text-white border'}} z-30 block w-full px-1 py-1 mb-0 transition-all rounded-lg ease-soft-in-out bg-inherit text-slate-700" nav-link active href="{{route('project')}}?v=1" role="tab" aria-selected="true">
                                    <svg class="text-slate-700" width="16px" height="16px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g transform="translate(-2319.000000, -291.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                <g transform="translate(1716.000000, 291.000000)">
                                                    <g transform="translate(603.000000, 0.000000)">
                                                        <path class="fill-slate-800 dark:fill-white" d="M22.7597136,19.3090182 L38.8987031,11.2395234 C39.3926816,10.9925342 39.592906,10.3918611 39.3459167,9.89788265 C39.249157,9.70436312 39.0922432,9.5474453 38.8987261,9.45068056 L20.2741875,0.1378125 L20.2741875,0.1378125 C19.905375,-0.04725 19.469625,-0.04725 19.0995,0.1378125 L3.1011696,8.13815822 C2.60720568,8.38517662 2.40701679,8.98586148 2.6540352,9.4798254 C2.75080129,9.67332903 2.90771305,9.83023153 3.10122239,9.9269862 L21.8652864,19.3090182 C22.1468139,19.4497819 22.4781861,19.4497819 22.7597136,19.3090182 Z"></path>
                                                        <path class="fill-slate-800 dark:fill-white" d="M23.625,22.429159 L23.625,39.8805372 C23.625,40.4328219 24.0727153,40.8805372 24.625,40.8805372 C24.7802551,40.8805372 24.9333778,40.8443874 25.0722402,40.7749511 L41.2741875,32.673375 L41.2741875,32.673375 C41.719125,32.4515625 42,31.9974375 42,31.5 L42,14.241659 C42,13.6893742 41.5522847,13.241659 41,13.241659 C40.8447549,13.241659 40.6916418,13.2778041 40.5527864,13.3472318 L24.1777864,21.5347318 C23.8390024,21.7041238 23.625,22.0503869 23.625,22.429159 Z" opacity="0.7"></path>
                                                        <path class="fill-slate-800 dark:fill-white" d="M20.4472136,21.5347318 L1.4472136,12.0347318 C0.953235098,11.7877425 0.352562058,11.9879669 0.105572809,12.4819454 C0.0361450918,12.6208008 6.47121774e-16,12.7739139 0,12.929159 L0,30.1875 L0,30.1875 C0,30.6849375 0.280875,31.1390625 0.7258125,31.3621875 L19.5528096,40.7750766 C20.0467945,41.0220531 20.6474623,40.8218132 20.8944388,40.3278283 C20.963859,40.1889789 21,40.0358742 21,39.8806379 L21,22.429159 C21,22.0503869 20.7859976,21.7041238 20.4472136,21.5347318 Z" opacity="0.7"></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <span class="ml-1 text-sx">Project</span>
                                </a>
                            </li>
                            <li class="z-30 flex-auto text-center">
                                <a class="{{app('request')->input ('display') == 'table'?'dark:text-white border':''}} z-30 block w-full px-1 py-1 mb-0 transition-all border-0 rounded-lg ease-soft-in-out bg-inherit text-slate-700" nav-link href="{{route('project')}}?v=1&display=table" role="tab" aria-selected="false">
                                    <svg class="text-slate-700" width="16px" height="16px" viewBox="0 0 40 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <title>table</title>
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                <g transform="translate(1716.000000, 291.000000)">
                                                    <g transform="translate(154.000000, 300.000000)">
                                                        <path class="fill-slate-800 dark:fill-white" d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z" opacity="0.603585379"></path>
                                                        <path class="fill-slate-800 dark:fill-white" d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z"></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                    <span class="ml-1 text-sx">Table</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(app('request')->input ('display') == "table")
    <div class="w-full p-6 mx-auto">
        <section class="py-4">
            <div class="flex flex-wrap -mx-3">
                <div class="w-full max-w-full px-3 mr-auto md:flex-0 shrink-0 md:w-8/12">
                    <!-- <h5 class="dark:text-white">Some of Our Awesome Projects</h5> -->
                    <!-- <p>This is the paragraph where you can write more details about your projects. Keep you user engaged by providing meaningful information.</p> -->
                </div>
            </div>
            <div class="justify-between sm:flex">
                <div>
                    @livewire('project.add')
                </div>
                <!-- <div class="flex">
                    <div class="relative inline">
                        <a href="javascript:;" dropdown-trigger="" class="inline-block px-6 py-3 mb-4 font-bold text-center uppercase align-middle transition-all bg-transparent border border-solid rounded-lg shadow-none cursor-pointer select-auto hover:scale-102 leading-pro text-xs ease-soft-in tracking-tight-soft active:opacity-85 active:shadow-soft-xs border-slate-700 text-slate-700 hover:border-slate-700 hover:bg-transparent hover:opacity-75 active:border-slate-700 active:bg-slate-700 active:text-white" aria-expanded="false">
                            Filters
                        </a>
                        <ul dropdown-menu="" class="z-100 min-w-44 text-sm shadow-soft-3xl duration-250 transform-dropdown before:duration-350 before:font-awesome before:ease-soft before:text-5.5 dark:bg-gray-950 pointer-events-none absolute top-1.5 right-2 left-auto m-0 mt-2 block origin-top cursor-pointer list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-2 py-4 text-left text-slate-500 opacity-0 transition-all will-change-transform before:absolute before:top-0 before:right-5 before:left-auto before:z-40 before:text-white before:transition-all before:content-['\f0d8'] sm:-mr-6">
                            <li class="relative"><a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg px-4 font-normal text-slate-500 transition-colors hover:bg-gray-200 hover:text-slate-700 focus:bg-gray-200 focus:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:duration-300" href="javascript:;">Status: Paid</a></li>
                            <li class="relative"><a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg px-4 font-normal text-slate-500 transition-colors hover:bg-gray-200 hover:text-slate-700 focus:bg-gray-200 focus:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:duration-300" href="javascript:;">Status: Refunded</a></li>
                            <li class="relative"><a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg px-4 font-normal text-slate-500 transition-colors hover:bg-gray-200 hover:text-slate-700 focus:bg-gray-200 focus:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:duration-300" href="javascript:;">Status: Canceled</a></li>
                            <li class="relative">
                                <hr class="h-px my-2 bg-gradient-to-r from-transparent via-black/40 to-transparent">
                            </li>
                            <li class="relative"><a class="py-1.2 text-danger lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg px-4 font-normal text-red-600 transition-colors hover:bg-gray-200 focus:bg-gray-200 dark:hover:bg-gray-200/80 lg:duration-300" href="javascript:;">Remove Filter</a></li>
                        </ul>
                    </div>
                    <button data-type="csv" type="button" export-button-list="" class="inline-block px-6 py-3 mb-4 ml-2 font-bold text-center uppercase align-middle transition-all bg-transparent border border-solid rounded-lg shadow-none cursor-pointer hover:scale-102 leading-pro text-xs ease-soft-in tracking-tight-soft active:opacity-85 active:shadow-soft-xs border-slate-700 text-slate-700 hover:border-slate-700 hover:bg-transparent hover:opacity-75 active:border-slate-700 active:bg-slate-700 active:text-white">
                        <span>
                            <i class="ni leading-none ni-archive-2"></i>
                        </span>
                        <span>Export CSV</span>
                    </button>
                </div> -->
            </div>

            <livewire:table.project-table searchable="name" exportable/>
        </section>

        @include('navigation.footer')

    </div>
    @else
    <div class="w-full p-6 mx-auto">
        <section class="py-4">
            <div class="flex flex-wrap -mx-3">
                <div class="w-full max-w-full px-3 mr-auto md:flex-0 shrink-0 md:w-8/12">
                    <h5 class="dark:text-white">Our Projects</h5>
                    <p>Details about your projects. Keep you user engaged by providing meaningful information.</p>
                </div>
            </div>
            <div class="flex flex-wrap mt-2 -mx-3 lg:mt-6">
                @foreach (auth()->user()->company->projects as $project)
                    <div class="w-full max-w-full px-3 mb-6 md:flex-0 shrink-0 md:w-6/12 lg:w-4/12">
                        <div class="relative flex flex-col min-w-0 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">
                            <div class="flex-auto p-4">
                                <div class="flex">
                                    <div class="w-19 h-19 text-base ease-soft-in-out bg-gradient-to-tl from-gray-900 to-slate-800 dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 inline-flex items-center justify-center rounded-lg p-2 text-white transition-all duration-200">
                                        <img class="w-full" src="https://ui-avatars.com/api/?name={{$project->name}}&amp;color=7F9CF5&amp;background=EBF4FF" alt="Image placeholder" />
                                    </div>
                                    <div class="my-auto ml-4">
                                        <h6 class="dark:text-white">{{$project->name}}</h6>
                                        <div>
                                            <a href="javascript:;" class="relative z-20 inline-flex items-center justify-center w-6 h-6 text-white transition-all duration-200 border-2 border-white border-solid ease-soft-in-out text-xs rounded-circle hover:z-30" data-target="tooltip_trigger">
                                                <img class="w-full rounded-circle" alt="Image placeholder" src="https://ui-avatars.com/api/?name={{$project->name}}&amp;color=7F9CF5&amp;background=EBF4FF" />
                                            </a>
                                            <div class="hidden px-2 py-1 text-white bg-black rounded-lg text-sm" id="tooltip" role="tooltip" data-popper-placement="bottom">
                                                Elena Morison
                                                <div class="invisible absolute h-2 w-2 bg-inherit before:visible before:absolute before:h-2 before:w-2 before:rotate-45 before:bg-inherit before:content-['']" data-popper-arrow></div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="ml-auto">
                                        <div class="relative">
                                            <button dropdown-trigger class="inline-block py-3 pl-0 pr-2 mb-4 font-bold text-center uppercase align-middle transition-all bg-transparent border-0 rounded-lg shadow-none cursor-pointer leading-pro text-xs ease-soft-in tracking-tight-soft bg-150 bg-x-25 text-slate-400 dark:text-white" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-lg" aria-hidden="true"></i>
                                            </button>
                                            <p class="hidden transform-dropdown-show"></p>
                                            <div dropdown-menu class="dark:shadow-soft-dark-xl z-100 dark:bg-gray-950 text-sm lg:shadow-soft-3xl duration-250 before:duration-350 before:font-awesome before:ease-soft min-w-44 before:text-5.5 transform-dropdown pointer-events-none absolute right-0 left-auto top-0 m-0 -mr-4 mt-2 list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-0 py-2 text-left text-slate-500 opacity-0 transition-all before:absolute before:top-0 before:right-7 before:left-auto before:z-40 before:text-white before:transition-all before:content-['\f0d8'] sm:-mr-6">
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="{{route('project.show', $project->id)}}">Edit</a>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Archive</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <p class="mt-4 leading-normal text-sm">{{$project->customer_name}}<br>{{$project->customer_address}}</p>
                                <hr class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />
                                <div class="flex flex-wrap -mx-3">
                                    <div class="w-6/12 max-w-full px-3 flex-0">
                                        <!-- <h6 class="mb-0 leading-normal text-sm">5</h6> -->
                                        <p class="mb-0 font-semibold leading-normal text-sm text-slate-400">{{$project->type}}</p>
                                    </div>
                                    <div class="w-6/12 max-w-full px-3 text-right flex-0">
                                        <h6 class="mb-0 leading-normal text-sm">{{$project->updated_at->format('d.m.Y')}}</h6>
                                        <p class="mb-0 font-semibold leading-normal text-sm text-slate-400">{{view('label.label', ['type' => $project->status])}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="w-full max-w-full px-3 mb-6 md:flex-0 shrink-0 md:w-6/12 lg:w-4/12">
                    <div class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="flex flex-col justify-center flex-auto p-6 text-center">
                            <div class="text-slate-400">
                                @livewire('project.add')
                            </div>
                            <!-- <a href="javascript:;">
                                <i class="mb-4 fa fa-plus text-slate-400"></i>
                                <h5 class="text-slate-400">New project</h5>
                            </a> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @include('navigation.footer')
    </div>
    @endif
@endsection
