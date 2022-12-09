@extends('layouts.side-menu')

@section('title', 'Project Details')

@section('sidebar')
@parent

<p>This is appended to the master sidebar.</p>
@endsection

@section('content')
<nav class="absolute z-20 flex flex-wrap items-center justify-between w-full px-6 py-2 text-white transition-all shadow-none duration-250 ease-soft-in lg:flex-nowrap lg:justify-start" navbar-profile navbar-scroll="true">
    <div class="flex items-center justify-between w-full px-6 py-1 mx-auto flex-wrap-inherit">
        <nav>
            <ol class="flex flex-wrap pt-1 pl-2 pr-4 mr-12 bg-transparent rounded-lg sm:mr-16">
                <li class="leading-normal text-sm">
                    <a class="text-white opacity-50" href="javascript:;">User</a>
                </li>
                <li class="text-sm pl-2 capitalize leading-normal before:float-left before:pr-2 before:content-['/']" aria-current="page">Project</li>
            </ol>
            <h6 class="mb-2 ml-2 font-bold text-white capitalize">Details</h6>
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
            @if(auth()->user()->currentTeam && auth()->user()->currentTeam->id == env('IN_HOUSE_TEAM_ID'))
            <!-- Global Search -->
            <div class="flex items-center md:ml-auto md:pr-4">
                <div class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease-soft">
                    <!-- <span class="text-sm ease-soft leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </span>
                        <input type="text" class="pl-9 text-sm focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 ease-soft w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 pr-3 text-gray-700 transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none focus:transition-shadow" placeholder="Type here..." /> -->
                    @livewire('search.all')
                </div>
            </div>
            @endif
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
                    <a href="javascript:;" class="p-0 text-white transition-all text-sm ease-soft-in-out">
                        <i fixed-plugin-button-nav class="cursor-pointer fa fa-cog" aria-hidden="true"></i>

                    </a>
                </li>

                <li class="relative flex items-center pr-2">
                    <p class="hidden transform-dropdown-show"></p>
                    <!-- <a dropdown-trigger href="javascript:;" class="block p-0 text-white transition-all text-sm ease-nav-brand dark:text-white" aria-expanded="false">
                        <i class="cursor-pointer fa fa-bell" aria-hidden="true"></i>
                    </a>
                    <ul dropdown-menu class="text-sm transform-dropdown before:font-awesome before:leading-default before:duration-350 before:ease-soft lg:shadow-soft-3xl duration-250 min-w-44 before:sm:right-7 before:text-5.5 dark:bg-gray-950 pointer-events-none absolute right-0 top-0 z-50 origin-top list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-2 py-4 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-2 before:left-auto before:top-0 before:z-50 before:inline-block before:font-normal before:text-white before:antialiased before:transition-all before:content-['\f0d8'] sm:-mr-6 lg:absolute lg:right-0 lg:left-auto lg:mt-2 lg:block lg:cursor-pointer">

                        <li class="relative mb-2">
                            <a class="group ease-soft py-1.2 clear-both block w-full whitespace-nowrap rounded-lg bg-transparent px-4 duration-300 hover:bg-gray-200 hover:text-slate-700 dark:hover:bg-gray-200/80 lg:transition-colors" href="javascript:;">
                                <div class="flex py-1">
                                    <div class="my-auto">
                                        <img src="../../../assets/img/team-2.jpg" class="inline-flex items-center justify-center mr-4 text-white text-sm h-9 w-9 max-w-none rounded-xl" />
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <h6 class="mb-1 font-normal leading-normal text-sm group-hover:text-slate-700 dark:text-white"><span class="font-semibold">New message</span> from Laur</h6>
                                        <p class="mb-0 leading-tight text-xs text-slate-400 group-hover:text-slate-700 dark:text-white dark:opacity-80">
                                            <i class="mr-1 fa fa-clock" aria-hidden="true"></i>
                                            13 minutes ago
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="relative mb-2">
                            <a class="group ease-soft py-1.2 clear-both block w-full whitespace-nowrap rounded-lg px-4 transition-colors duration-300 hover:bg-gray-200 hover:text-slate-700 dark:hover:bg-gray-200/80" href="javascript:;">
                                <div class="flex py-1">
                                    <div class="my-auto">
                                        <img src="../../../assets/img/small-logos/logo-spotify.svg" class="inline-flex items-center justify-center mr-4 text-white text-sm bg-gradient-to-tl from-gray-900 to-slate-800 dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 h-9 w-9 max-w-none rounded-xl" />
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <h6 class="mb-1 font-normal leading-normal text-sm group-hover:text-slate-700 dark:text-white"><span class="font-semibold">New album</span> by Travis Scott</h6>
                                        <p class="mb-0 leading-tight text-xs text-slate-400 group-hover:text-slate-700 dark:text-white dark:opacity-80">
                                            <i class="mr-1 fa fa-clock" aria-hidden="true"></i>
                                            1 day
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="relative">
                            <a class="group ease-soft py-1.2 clear-both block w-full whitespace-nowrap rounded-lg px-4 transition-colors duration-300 hover:bg-gray-200 hover:text-slate-700 dark:hover:bg-gray-200/80" href="javascript:;">
                                <div class="flex py-1">
                                    <div class="inline-flex items-center justify-center my-auto mr-4 text-white transition-all duration-200 ease-nav-brand text-sm bg-gradient-to-tl from-slate-600 to-slate-300 h-9 w-9 rounded-xl">
                                        <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>credit-card</title>
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                    <g transform="translate(1716.000000, 291.000000)">
                                                        <g transform="translate(453.000000, 454.000000)">
                                                            <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                                                            <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <h6 class="mb-1 font-normal leading-normal text-sm group-hover:text-slate-700 dark:text-white">Payment successfully completed</h6>
                                        <p class="mb-0 leading-tight text-xs text-slate-400 group-hover:text-slate-700 dark:text-white dark:opacity-80">
                                            <i class="mr-1 fa fa-clock" aria-hidden="true"></i>
                                            2 days
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul> -->
                    @livewire('notification-app', ['client_id' => Auth::user()->id], key(Auth::user()->id))
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="w-full px-6 mx-auto">
    <div class="min-h-75 relative mt-0 flex items-center overflow-hidden rounded-sm bg-[url('../../assets/img/curved-images/curved0.jpg')] bg-cover bg-center p-0">
        <span class="absolute inset-y-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-purple-700 to-slate-800 opacity-60"></span>
    </div>
    <div class="relative flex flex-col flex-auto min-w-0 p-4 mx-6 -mt-16 overflow-hidden break-words border-0 shadow-blur dark:shadow-soft-dark-xl dark:bg-gray-950 rounded-2xl bg-white/80 bg-clip-border backdrop-blur-2xl backdrop-saturate-200">
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-auto max-w-full px-3">
                <div class="text-base ease-soft-in-out h-19 w-19 relative inline-flex items-center justify-center rounded-xl text-white transition-all duration-200">
                    <img src="{{$user->profile_photo_url?$user->profile_photo_url:'https://ui-avatars.com/api/?name='.$project->name.'&amp;color=7F9CF5&amp;background=EBF4FF'}}" alt="{{$user->name}}" class="w-full shadow-soft-sm rounded-xl">
                </div>
            </div>
            <div class="flex-none w-auto max-w-full px-3 my-auto">
                <div class="h-full">
                    <h5 class="mb-1 dark:text-white">{{$project->name}}</h5>
                    <p class="mb-0 font-semibold leading-normal text-sm dark:text-white dark:opacity-60"></p>
                </div>
            </div>
            <div class="w-full max-w-full px-3 mx-auto mt-4 sm:my-auto sm:mr-0 md:w-1/2 md:flex-none lg:w-5/12">
                <div class="relative right-0">
                    <ul class="relative flex p-1 list-none bg-transparent rounded-xl" role="list">
                        <li class="z-30 flex-auto text-center">
                            <a class="border z-30 block w-full px-1 py-1 mb-0 transition-all rounded-lg ease-soft-in-out bg-inherit text-slate-700 dark:text-white" nav-link active href="{{route('project.show', $project->id)}}?v=1&companyid={{$user->id}}" role="tab" aria-selected="true">
                                <svg class="text-slate-700" width="16px" height="16px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>project</title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-2319.000000, -291.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                            <g transform="translate(1716.000000, 291.000000)">
                                                <g transform="translate(603.000000, 0.000000)">
                                                    <path class="fill-slate-800 {{!app('request')->input('page')?'dark:fill-white':''}}" d="M22.7597136,19.3090182 L38.8987031,11.2395234 C39.3926816,10.9925342 39.592906,10.3918611 39.3459167,9.89788265 C39.249157,9.70436312 39.0922432,9.5474453 38.8987261,9.45068056 L20.2741875,0.1378125 L20.2741875,0.1378125 C19.905375,-0.04725 19.469625,-0.04725 19.0995,0.1378125 L3.1011696,8.13815822 C2.60720568,8.38517662 2.40701679,8.98586148 2.6540352,9.4798254 C2.75080129,9.67332903 2.90771305,9.83023153 3.10122239,9.9269862 L21.8652864,19.3090182 C22.1468139,19.4497819 22.4781861,19.4497819 22.7597136,19.3090182 Z"></path>
                                                    <path class="fill-slate-800 {{!app('request')->input('page')?'dark:fill-white':''}}" d="M23.625,22.429159 L23.625,39.8805372 C23.625,40.4328219 24.0727153,40.8805372 24.625,40.8805372 C24.7802551,40.8805372 24.9333778,40.8443874 25.0722402,40.7749511 L41.2741875,32.673375 L41.2741875,32.673375 C41.719125,32.4515625 42,31.9974375 42,31.5 L42,14.241659 C42,13.6893742 41.5522847,13.241659 41,13.241659 C40.8447549,13.241659 40.6916418,13.2778041 40.5527864,13.3472318 L24.1777864,21.5347318 C23.8390024,21.7041238 23.625,22.0503869 23.625,22.429159 Z" opacity="0.7"></path>
                                                    <path class="fill-slate-800 {{!app('request')->input('page')?'dark:fill-white':''}}" d="M20.4472136,21.5347318 L1.4472136,12.0347318 C0.953235098,11.7877425 0.352562058,11.9879669 0.105572809,12.4819454 C0.0361450918,12.6208008 6.47121774e-16,12.7739139 0,12.929159 L0,30.1875 L0,30.1875 C0,30.6849375 0.280875,31.1390625 0.7258125,31.3621875 L19.5528096,40.7750766 C20.0467945,41.0220531 20.6474623,40.8218132 20.8944388,40.3278283 C20.963859,40.1889789 21,40.0358742 21,39.8806379 L21,22.429159 C21,22.0503869 20.7859976,21.7041238 20.4472136,21.5347318 Z" opacity="0.7"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="ml-1 text-sx">Project</span>
                            </a>
                        </li>
                        <li class="z-30 flex-auto text-center">
                            <a class="z-30 block w-full px-1 py-1 mb-0 transition-all border-0 rounded-lg ease-soft-in-out bg-inherit text-slate-700" nav-link href="{{route('project.show', $project->id)

                            }}?v=1&companyid={{$user->id}}&page=contract" role="tab" aria-selected="false">
                                <svg class="text-slate-700" width="16px" height="16px" viewBox="0 0 40 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>contract</title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                            <g transform="translate(1716.000000, 291.000000)">
                                                <g transform="translate(154.000000, 300.000000)">
                                                    <path class="fill-slate-800 {{app('request')->input('page')=='contract'?'dark:fill-white':''}}" d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z" opacity="0.603585379"></path>
                                                    <path class="fill-slate-800 {{app('request')->input('page')=='contract'?'dark:fill-white':''}}" d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="ml-1 text-sx">Contract</span>
                            </a>
                        </li>
                        <li class="z-30 flex-auto text-center">
                            <a class="z-30 block w-full px-1 py-1 mb-0 transition-colors border-0 rounded-lg ease-soft-in-out bg-inherit text-slate-700" nav-link href="{{route('project.show', $project->id)}}?v=1&companyid={{$user->id}}&page=quotation" role="tab" aria-selected="false">
                                <svg class="text-slate-700" width="16px" height="16px" viewBox="0 0 40 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>quotation</title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                            <g transform="translate(1716.000000, 291.000000)">
                                                <g transform="translate(154.000000, 300.000000)">
                                                    <path class="fill-slate-800 {{app('request')->input('page')=='quotation'?'dark:fill-white':''}}" d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z" opacity="0.603585379"></path>
                                                    <path class="fill-slate-800 {{app('request')->input('page')=='quotation'?'dark:fill-white':''}}" d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="ml-1 text-sx">Quotation</span>
                            </a>
                        </li>
                        <li class="z-30 flex-auto text-center">
                            <a class="z-30 block w-full px-1 py-1 mb-0 transition-colors border-0 rounded-lg ease-soft-in-out bg-inherit text-slate-700" nav-link href="{{route('project.show', $project->id)}}?v=1&companyid={{$user->id}}&page=order" role="tab" aria-selected="false">
                                <svg class="text-dark" width="12px" height="12px" viewBox="0 0 42 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>order</title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-1869.000000, -741.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                            <g transform="translate(1716.000000, 291.000000)">
                                                <g id="billings" transform="translate(153.000000, 450.000000)">
                                                    <path class="fill-slate-800 {{app('request')->input('page')=='order'?'dark:fill-white':''}}" d="M34.080375,13.125 L27.3748125,1.9490625 C27.1377583,1.53795093 26.6972449,1.28682264 26.222716,1.29218729 C25.748187,1.29772591 25.3135593,1.55890827 25.0860125,1.97535742 C24.8584658,2.39180657 24.8734447,2.89865282 25.1251875,3.3009375 L31.019625,13.125 L10.980375,13.125 L16.8748125,3.3009375 C17.1265553,2.89865282 17.1415342,2.39180657 16.9139875,1.97535742 C16.6864407,1.55890827 16.251813,1.29772591 15.777284,1.29218729 C15.3027551,1.28682264 14.8622417,1.53795093 14.6251875,1.9490625 L7.919625,13.125 L0,13.125 L0,18.375 L42,18.375 L42,13.125 L34.080375,13.125 Z" opacity="0.595377604"></path>
                                                    <path class="fill-slate-800 {{app('request')->input('page')=='order'?'dark:fill-white':''}}" d="M3.9375,21 L3.9375,38.0625 C3.9375,40.9619949 6.28800506,43.3125 9.1875,43.3125 L32.8125,43.3125 C35.7119949,43.3125 38.0625,40.9619949 38.0625,38.0625 L38.0625,21 L3.9375,21 Z M14.4375,36.75 L11.8125,36.75 L11.8125,26.25 L14.4375,26.25 L14.4375,36.75 Z M22.3125,36.75 L19.6875,36.75 L19.6875,26.25 L22.3125,26.25 L22.3125,36.75 Z M30.1875,36.75 L27.5625,36.75 L27.5625,26.25 L30.1875,26.25 L30.1875,36.75 Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="ml-1 text-sx">Order</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="w-full p-6 mx-auto">
    <section class="py-4">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full max-w-full px-3 flex-0">
                <div class="relative flex flex-col min-w-0 overflow-none break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="flex flex-auto p-6">
                        <div class="w-4/12 text-center flex-0 sm:w-3/12 md:w-2/12 lg:w-1/12">
                            <a title="add member" href="javascript:;" class="ease-soft-in-out w-14 h-14 text-sm rounded-circle bg-gradient-to-tl from-purple-700 to-slate-800 inline-flex items-center justify-center border-0 text-white transition-all duration-200">
                                <i class="text-white fas fa-plus" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div class="w-4/12 text-center flex-0 sm:w-3/12 md:w-2/12 lg:w-1/12">
                            <a href="javascript:;" class="ease-soft-in-out w-14 h-14 text-sm rounded-circle inline-flex items-center justify-center border border-solid border-fuchsia-500 text-white transition-all duration-200">
                                <img src="https://ui-avatars.com/api/?name=abbie w&amp;color=7F9CF5&amp;background=EBF4FF" alt="Image placeholder" class="w-full p-1 rounded-circle">
                            </a>
                            <p class="mb-0 leading-normal text-sm dark:text-white dark:opacity-60">Abbie W</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap mt-6 -mx-3">
            <div class="max-w-full px-3 lg:flex-0 lg:w-8/12">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="flex items-center px-6 py-4 border-b border-solid rounded-t-2xl border-b-slate-100">
                        <div class="flex items-center">
                            <a href="javascript:;">
                                <img src="https://ui-avatars.com/api/?name={{$user->name}}&amp;color=7F9CF5&amp;background=EBF4FF" alt="profile-image" class="inline-flex items-center justify-center w-12 h-12 text-white transition-all duration-200 text-base ease-soft-in-out rounded-xl">
                            </a>
                            <div class="mx-4">
                                <a href="javascript:;" class="leading-normal text-sm text-slate-700 dark:text-white">{{$user->name}}</a>
                                <small class="block text-slate-500">{{$project->created_at->diffForHumans()}}</small>
                            </div>
                        </div>
                        <!-- <div class="ml-auto text-right">
                            <button class="inline-block px-8 py-2 mb-0 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer hover:scale-102 hover:shadow-soft-xs active:opacity-85 tracking-tight-soft leading-pro ease-soft-in text-xs shadow-soft-md bg-150 bg-x-25 bg-gradient-to-tl from-purple-700 to-pink-500">
                                <i class="pr-2 fas fa-plus text-3xs" aria-hidden="true"></i>
                                Follow
                            </button>
                        </div> -->
                    </div>
                    <div class="flex-auto p-6">
                        <div class="mb-6 dark:text-white dark:opacity-60">
                            @if(app('request')->input('page')=='order')
                                @livewire('project.orders', ['id' => $project->id])
                            @elseif(app('request')->input('page')=='quotation')
                                @livewire('project.quotations', ['id' => $project->id])
                            @elseif(app('request')->input('page')=='contract')
                                @livewire('project.contracts', ['id' => $project->id])
                            @else
                                @livewire('project.edit', ['uuid'=>$project->id])
                                @livewire('project.add-customer', ['id' => $project->id])
                                @livewire('project.edit-type', ['id' => $project->id, 'disabled' => disableInput($project->status)])
                            @endif
                        </div>
                        <div class="mb-1">
                            <div class="flex">
                                <div class="shrink-0">
                                    <img src="https://ui-avatars.com/api/?name=abbie w&amp;color=7F9CF5&amp;background=EBF4FF" alt="Image placeholder" class="inline-flex items-center justify-center w-12 h-12 text-white transition-all duration-200 text-base ease-soft-in-out rounded-circle">
                                </div>
                                <div class="ml-4 grow">
                                    <h5 class="mt-0 mb-2 dark:text-white">Michael Lewis</h5>
                                    <p class="leading-normal text-sm dark:text-white dark:opacity-60">I always felt like I could do anything. That’s the main thing people are controlled by! Thoughts- their perception of themselves!</p>
                                    <div class="flex">
                                        <div>
                                            <i class="mr-1 cursor-pointer ni leading-none ni-like-2"></i>
                                        </div>
                                        <span class="mr-2 leading-normal text-sm">3 likes</span>
                                        <div>
                                            <i class="mr-1 cursor-pointer ni leading-none ni-curved-next"></i>
                                        </div>
                                        <span class="mr-2 leading-normal text-sm">2 shares</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex mt-6">
                                <div class="shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{auth()->user()->name}}&amp;color=7F9CF5&amp;background=EBF4FF" alt="Image placeholder" class="inline-flex items-center justify-center w-12 h-12 mr-4 text-white transition-all duration-200 text-base ease-soft-in-out rounded-circle">
                                </div>
                                <div class="my-auto grow">
                                    <form>
                                        <textarea rows="1" placeholder="Write your comment" class="focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease-soft min-h-unset block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding px-3 py-2 font-normal text-gray-700 transition placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none"></textarea>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full max-w-full px-3 flex-0 lg:w-4/12">
                <div class="relative flex flex-col min-w-0 mt-6 mb-4 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border lg:mt-0">
                    <div class="flex-auto p-6 pb-0">
                        @livewire('project.progress', ['uuid'=>$project->id,'theme'=>1])
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('navigation.footer')
</div>

@endsection
