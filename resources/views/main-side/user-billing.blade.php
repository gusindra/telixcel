@extends('layouts.side-menu')

@section('title', 'Dashboard')

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
                    <span class="text-sm ease-soft leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </span>
                    <input type="text" class="pl-9 text-sm focus:shadow-soft-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 ease-soft w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 pr-3 text-gray-700 transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none focus:transition-shadow" placeholder="Type here..." />
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
                    <a href="javascript:;" class="p-0 text-white transition-all text-sm ease-soft-in-out">
                        <i fixed-plugin-button-nav class="cursor-pointer fa fa-cog" aria-hidden="true"></i>

                    </a>
                </li>

                <li class="relative flex items-center pr-2">
                    <p class="hidden transform-dropdown-show"></p>
                    <a dropdown-trigger href="javascript:;" class="block p-0 text-white transition-all text-sm ease-nav-brand dark:text-white" aria-expanded="false">
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
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="w-full px-6 mx-auto">
    <div class="min-h-75 relative mt-0 flex items-center overflow-hidden rounded-sm bg-[url('../../assets/img/curved-images/curved0.jpg')] bg-cover bg-center p-0">
        <span class="absolute inset-y-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-purple-700 to-pink-500 opacity-60"></span>
    </div>
    <div class="relative flex flex-col flex-auto min-w-0 p-4 mx-6 -mt-16 overflow-hidden break-words border-0 shadow-blur dark:shadow-soft-dark-xl dark:bg-gray-950 rounded-2xl bg-white/80 bg-clip-border backdrop-blur-2xl backdrop-saturate-200">
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-auto max-w-full px-3">
                <div class="text-base ease-soft-in-out h-19 w-19 relative inline-flex items-center justify-center rounded-xl text-white transition-all duration-200">
                    <img src="{{$user->profile_photo_url?$user->profile_photo_url:'https://ui-avatars.com/api/?name='.$user->name.'&amp;color=7F9CF5&amp;background=EBF4FF'}}" alt="{{$user->name}}" class="w-full shadow-soft-sm rounded-xl">
                </div>
            </div>
            <!-- {{$user}} -->
            <div class="flex-none w-auto max-w-full px-3 my-auto">
                <div class="h-full">
                    <h5 class="mb-1 dark:text-white">{{$user->name}}</h5>
                    <p class="mb-0 font-semibold leading-normal text-sm dark:text-white dark:opacity-60">{{$user->person_in_charge}}</p>
                </div>
            </div>
            <div class="w-full max-w-full px-3 mx-auto mt-4 sm:my-auto sm:mr-0 md:w-1/2 md:flex-none lg:w-4/12">
                <div class="relative right-0">
                    <ul class="relative flex flex-wrap p-1 list-none bg-transparent rounded-xl" nav-pills role="list">
                        <li class="z-30 flex-auto text-center">
                            <a class="z-30 block w-full px-0 py-1 mb-0 transition-all border-0 rounded-lg ease-soft-in-out bg-inherit text-slate-700 dark:text-white" nav-link active href="javascript:;" role="tab" aria-selected="true">
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
                                <span class="ml-1">Project</span>
                            </a>
                        </li>
                        <li class="z-30 flex-auto text-center">
                            <a class="z-30 block w-full px-0 py-1 mb-0 transition-all border-0 rounded-lg ease-soft-in-out bg-inherit text-slate-700" nav-link href="javascript:;" role="tab" aria-selected="false">
                                <svg class="text-slate-700" width="16px" height="16px" viewBox="0 0 40 44" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>document</title>
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
                                <span class="ml-1">Customer</span>
                            </a>
                        </li>
                        <li class="z-30 flex-auto text-center">
                            <a class="z-30 block w-full px-0 py-1 mb-0 transition-colors border-0 rounded-lg ease-soft-in-out bg-inherit text-slate-700" nav-link href="javascript:;" role="tab" aria-selected="false">
                                <svg class="text-slate-700" width="16px" height="16px" viewBox="0 0 40 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <title>settings</title>
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-2020.000000, -442.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                            <g transform="translate(1716.000000, 291.000000)">
                                                <g transform="translate(304.000000, 151.000000)">
                                                    <polygon class="fill-slate-800 dark:fill-white" opacity="0.596981957" points="18.0883333 15.7316667 11.1783333 8.82166667 13.3333333 6.66666667 6.66666667 0 0 6.66666667 6.66666667 13.3333333 8.82166667 11.1783333 15.315 17.6716667"></polygon>
                                                    <path class="fill-slate-800 dark:fill-white" d="M31.5666667,23.2333333 C31.0516667,23.2933333 30.53,23.3333333 30,23.3333333 C29.4916667,23.3333333 28.9866667,23.3033333 28.48,23.245 L22.4116667,30.7433333 L29.9416667,38.2733333 C32.2433333,40.575 35.9733333,40.575 38.275,38.2733333 L38.275,38.2733333 C40.5766667,35.9716667 40.5766667,32.2416667 38.275,29.94 L31.5666667,23.2333333 Z" opacity="0.596981957"></path>
                                                    <path class="fill-slate-800 dark:fill-white" d="M33.785,11.285 L28.715,6.215 L34.0616667,0.868333333 C32.82,0.315 31.4483333,0 30,0 C24.4766667,0 20,4.47666667 20,10 C20,10.99 20.1483333,11.9433333 20.4166667,12.8466667 L2.435,27.3966667 C0.95,28.7083333 0.0633333333,30.595 0.00333333333,32.5733333 C-0.0583333333,34.5533333 0.71,36.4916667 2.11,37.89 C3.47,39.2516667 5.27833333,40 7.20166667,40 C9.26666667,40 11.2366667,39.1133333 12.6033333,37.565 L27.1533333,19.5833333 C28.0566667,19.8516667 29.01,20 30,20 C35.5233333,20 40,15.5233333 40,10 C40,8.55166667 39.685,7.18 39.1316667,5.93666667 L33.785,11.285 Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="ml-1">Settings</span>
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
            <div class="w-full max-w-full px-3 mr-auto md:flex-0 shrink-0 md:w-8/12">
                <h5 class="dark:text-white">Our Projects</h5>
                <p>Details about your projects. Keep you user engaged by providing meaningful information.</p>
            </div>
        </div>
        <div class="flex flex-wrap mt-2 -mx-3 lg:mt-6">
            @foreach ($user->projects as $project)
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

<div class="w-full p-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 flex-0 lg:w-6/12">
            <div class="flex flex-wrap -mx-3">
                <div class="w-full max-w-full px-3 flex-0 md:w-6/12">
                    <div class="dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl relative flex min-w-0 flex-col break-words rounded-2xl border-0 bg-white bg-[url('../../assets/img/curved-images/white-curved.jpg')] bg-clip-border">
                        <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 rounded-2xl opacity-90"> </span>
                        <div class="relative flex-auto p-4">
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-8/12 max-w-full px-3 text-left flex-0">
                                    <div class="inline-block w-12 h-12 text-center text-black bg-white bg-center rounded-lg fill-current stroke-none shadow-soft-2xl">
                                        <i class="ni leading-none ni-circle-08 bg-gradient-to-tl from-gray-900 to-slate-800 text-lg relative top-3.5 z-10 bg-clip-text text-transparent"></i>
                                    </div>
                                    <h5 class="mt-4 mb-0 font-bold text-white">1600</h5>
                                    <span class="leading-normal text-white text-sm">Users Active</span>
                                </div>
                                <div class="w-4/12 max-w-full px-3 flex-0">
                                    <div class="relative mb-16 text-right">
                                        <a href="javascript:;" class="cursor-pointer" dropdown-trigger="" aria-expanded="false">
                                            <i class="text-white fa fa-ellipsis-h" aria-hidden="true"></i>
                                        </a>
                                        <p class="hidden transform-dropdown-show"></p>
                                        <ul dropdown-menu="" class="dark:shadow-soft-dark-xl z-100 dark:bg-gray-950 text-sm lg:shadow-soft-3xl duration-250 before:duration-350 before:font-awesome before:ease-soft min-w-44 before:text-5.5 transform-dropdown pointer-events-none absolute right-0 left-auto top-0 m-0 -mr-4 mt-0 list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-0 py-2 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-7 before:left-auto before:top-0 before:z-40 before:text-white before:transition-all before:content-['\f0d8'] sm:-mr-6">
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Another action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Something else here</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="mt-auto mb-0 font-bold leading-normal text-right text-white text-sm dark:text-white/60">+55%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full max-w-full px-3 mt-6 flex-0 md:mt-0 md:w-6/12">
                    <div class="dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl relative flex min-w-0 flex-col break-words rounded-2xl border-0 bg-white bg-[url('../../assets/img/curved-images/white-curved.jpg')] bg-clip-border">
                        <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 rounded-2xl opacity-90"> </span>
                        <div class="relative flex-auto p-4">
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-8/12 max-w-full px-3 text-left flex-0">
                                    <div class="inline-block w-12 h-12 text-center text-black bg-white bg-center rounded-lg fill-current stroke-none shadow-soft-2xl">
                                        <i class="ni leading-none ni-active-40 bg-gradient-to-tl from-gray-900 to-slate-800 text-lg relative top-3.5 z-10 bg-clip-text text-transparent"></i>
                                    </div>
                                    <h5 class="mt-4 mb-0 font-bold text-white">357</h5>
                                    <span class="leading-normal text-white text-sm">Click Events</span>
                                </div>
                                <div class="w-4/12 max-w-full px-3 flex-0">
                                    <div class="relative mb-16 text-right">
                                        <a href="javascript:;" class="cursor-pointer" dropdown-trigger="" aria-expanded="false">
                                            <i class="text-white fa fa-ellipsis-h" aria-hidden="true"></i>
                                        </a>
                                        <p class="hidden transform-dropdown-show"></p>
                                        <ul dropdown-menu="" class="dark:shadow-soft-dark-xl z-100 dark:bg-gray-950 text-sm lg:shadow-soft-3xl duration-250 before:duration-350 before:font-awesome before:ease-soft min-w-44 before:text-5.5 transform-dropdown pointer-events-none absolute right-0 left-auto top-0 m-0 -mr-4 mt-0 list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-0 py-2 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-7 before:left-auto before:top-0 before:z-40 before:text-white before:transition-all before:content-['\f0d8'] sm:-mr-6">
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Another action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Something else here</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="mt-auto mb-0 font-bold leading-normal text-right text-white text-sm dark:text-white/60">+124%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap mt-6 -mx-3">
                <div class="w-full max-w-full px-3 flex-0 md:w-6/12">
                    <div class="dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl relative flex min-w-0 flex-col break-words rounded-2xl border-0 bg-white bg-[url('../../assets/img/curved-images/white-curved.jpg')] bg-clip-border">
                        <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 rounded-2xl opacity-90"> </span>
                        <div class="relative flex-auto p-4">
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-8/12 max-w-full px-3 text-left flex-0">
                                    <div class="inline-block w-12 h-12 text-center text-black bg-white bg-center rounded-lg fill-current stroke-none shadow-soft-2xl">
                                        <i class="ni leading-none ni-cart bg-gradient-to-tl from-gray-900 to-slate-800 text-lg relative top-3.5 z-10 bg-clip-text text-transparent"></i>
                                    </div>
                                    <h5 class="mt-4 mb-0 font-bold text-white">2300</h5>
                                    <span class="leading-normal text-white text-sm">Purchases</span>
                                </div>
                                <div class="w-4/12 max-w-full px-3 flex-0">
                                    <div class="relative mb-16 text-right">
                                        <a href="javascript:;" class="cursor-pointer" dropdown-trigger="" aria-expanded="false">
                                            <i class="text-white fa fa-ellipsis-h" aria-hidden="true"></i>
                                        </a>
                                        <p class="hidden transform-dropdown-show"></p>
                                        <ul dropdown-menu="" class="dark:shadow-soft-dark-xl z-100 dark:bg-gray-950 text-sm lg:shadow-soft-3xl duration-250 before:duration-350 before:font-awesome before:ease-soft min-w-44 before:text-5.5 transform-dropdown pointer-events-none absolute right-0 left-auto top-0 m-0 -mr-4 mt-0 list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-0 py-2 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-7 before:left-auto before:top-0 before:z-40 before:text-white before:transition-all before:content-['\f0d8'] sm:-mr-6">
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Another action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Something else here</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="mt-auto mb-0 font-bold leading-normal text-right text-white text-sm dark:text-white/60">+15%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full max-w-full px-3 mt-6 flex-0 md:mt-0 md:w-6/12">
                    <div class="dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl relative flex min-w-0 flex-col break-words rounded-2xl border-0 bg-white bg-[url('../../assets/img/curved-images/white-curved.jpg')] bg-clip-border">
                        <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 rounded-2xl opacity-90"> </span>
                        <div class="relative flex-auto p-4">
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-8/12 max-w-full px-3 text-left flex-0">
                                    <div class="inline-block w-12 h-12 text-center text-black bg-white bg-center rounded-lg fill-current stroke-none shadow-soft-2xl">
                                        <i class="ni leading-none ni-like-2 bg-gradient-to-tl from-gray-900 to-slate-800 text-lg relative top-3.5 z-10 bg-clip-text text-transparent"></i>
                                    </div>
                                    <h5 class="mt-4 mb-0 font-bold text-white">940</h5>
                                    <span class="leading-normal text-white text-sm">Likes</span>
                                </div>
                                <div class="w-4/12 max-w-full px-3 flex-0">
                                    <div class="relative mb-16 text-right">
                                        <a href="javascript:;" class="cursor-pointer" dropdown-trigger="" aria-expanded="false">
                                            <i class="text-white fa fa-ellipsis-h" aria-hidden="true"></i>
                                        </a>
                                        <p class="hidden transform-dropdown-show"></p>
                                        <ul dropdown-menu="" class="dark:shadow-soft-dark-xl z-100 dark:bg-gray-950 text-sm lg:shadow-soft-3xl duration-250 before:duration-350 before:font-awesome before:ease-soft min-w-44 before:text-5.5 transform-dropdown pointer-events-none absolute right-0 left-auto top-0 m-0 -mr-4 mt-0 list-none rounded-lg border-0 border-solid border-transparent bg-white bg-clip-padding px-0 py-2 text-left text-slate-500 opacity-0 transition-all before:absolute before:right-7 before:left-auto before:top-0 before:z-40 before:text-white before:transition-all before:content-['\f0d8'] sm:-mr-6">
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Another action</a>
                                            </li>
                                            <li>
                                                <a class="py-1.2 lg:ease-soft clear-both block w-full whitespace-nowrap rounded-lg border-0 bg-transparent px-4 text-left font-normal text-slate-500 hover:bg-gray-200 hover:text-slate-700 dark:text-white dark:hover:bg-gray-200/80 dark:hover:text-slate-700 lg:transition-colors lg:duration-300" href="javascript:;">Something else here</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="mt-auto mb-0 font-bold leading-normal text-right text-white text-sm dark:text-white/60">+90%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full max-w-full px-3 flex-0 lg:w-6/12">
            <div class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-4 pb-0 mb-0 rounded-t-2xl">
                    <h6 class="mb-0 dark:text-white">Reviews</h6>
                </div>
                <div class="flex-auto p-4 pb-0">
                    <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                        <li class="relative flex items-center px-0 py-2 mb-0 border-0 rounded-t-inherit text-inherit">
                            <div class="w-full">
                                <div class="flex mb-2">
                                    <span class="mr-2 font-semibold leading-normal capitalize text-sm">Positive Reviews</span>
                                    <span class="ml-auto font-semibold leading-normal text-sm">80%</span>
                                </div>
                                <div>
                                    <div class="h-0.75 text-xs flex overflow-visible rounded-lg bg-gray-200">
                                        <div class="bg-gradient-to-tl from-blue-600 to-cyan-400 w-80/100 transition-width duration-600 ease-soft rounded-1 -mt-0.4 -ml-px flex h-1.5 flex-col justify-center overflow-hidden whitespace-nowrap text-center text-white"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="relative flex items-center px-0 py-2 mb-2 border-0 text-inherit">
                            <div class="w-full">
                                <div class="flex mb-2">
                                    <span class="mr-2 font-semibold leading-normal capitalize text-sm">Neutral Reviews</span>
                                    <span class="ml-auto font-semibold leading-normal text-sm">17%</span>
                                </div>
                                <div>
                                    <div class="h-0.75 text-xs flex overflow-visible rounded-lg bg-gray-200">
                                        <div class="dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 bg-gradient-to-tl from-gray-900 to-slate-800 w-17/100 transition-width duration-600 ease-soft rounded-1 -mt-0.4 -ml-px flex h-1.5 flex-col justify-center overflow-hidden whitespace-nowrap text-center text-white"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="relative flex items-center px-0 py-2 mb-2 border-0 rounded-b-inherit text-inherit">
                            <div class="w-full">
                                <div class="flex mb-2">
                                    <span class="mr-2 font-semibold leading-normal capitalize text-sm">Negative Reviews</span>
                                    <span class="ml-auto font-semibold leading-normal text-sm">3%</span>
                                </div>
                                <div>
                                    <div class="h-0.75 text-xs flex overflow-visible rounded-lg bg-gray-200">
                                        <div class="bg-gradient-to-tl from-red-600 to-rose-400 w-3/100 transition-width duration-600 ease-soft rounded-1 -mt-0.4 -ml-px flex h-1.5 flex-col justify-center overflow-hidden whitespace-nowrap text-center text-white"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="flex items-center p-4 pt-0 rounded-b-2xl">
                    <div class="w-3/5">
                        <p class="leading-normal text-sm dark:text-white/60">
                            More than
                            <b>1,500,000</b>
                            developers used Creative Tim's products and over
                            <b>700,000</b>
                            projects were created.
                        </p>
                    </div>
                    <div class="w-2/5 text-right">
                        <a href="javascript:;" class="inline-block px-6 py-3 mb-0 font-bold text-right text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs dark:bg-gradient-to-tl dark:from-slate-850 dark:to-gray-850 bg-gradient-to-tl from-gray-900 to-slate-800 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">View all reviews</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-wrap my-6 -mx-3">
        <div class="w-full max-w-full px-3 flex-0">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="overflow-x-auto ps">
                    <table class="items-center w-full mb-0 align-top border-gray-200 text-slate-500 dark:border-white/40">
                        <thead class="align-bottom">
                            <tr>
                                <th class="px-6 py-3 font-bold uppercase align-middle bg-transparent border-b border-gray-200 border-solid shadow-none text-xxs tracking-none whitespace-nowrap text-slate-400 opacity-70 dark:border-white/40 dark:text-white dark:opacity-80">name</th>
                                <th class="px-6 py-3 pl-2 font-bold uppercase align-middle bg-transparent border-b border-gray-200 border-solid shadow-none text-xxs tracking-none whitespace-nowrap text-slate-400 opacity-70 dark:border-white/40 dark:text-white dark:opacity-80">function</th>
                                <th class="px-6 py-3 pl-2 font-bold uppercase align-middle bg-transparent border-b border-gray-200 border-solid shadow-none text-xxs tracking-none whitespace-nowrap text-slate-400 opacity-70 dark:border-white/40 dark:text-white dark:opacity-80">review</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 border-solid shadow-none text-xxs tracking-none whitespace-nowrap text-slate-400 opacity-70 dark:border-white/40 dark:text-white dark:opacity-80">email</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 border-solid shadow-none text-xxs tracking-none whitespace-nowrap text-slate-400 opacity-70 dark:border-white/40 dark:text-white dark:opacity-80">employed</th>
                                <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 border-solid shadow-none text-xxs tracking-none whitespace-nowrap text-slate-400 opacity-70 dark:border-white/40 dark:text-white dark:opacity-80">id</th>
                            </tr>
                        </thead>
                        <tbody class="border-t-2 border-current border-solid dark:border-white/40">
                            <tr>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <div class="flex px-2 py-1">
                                        <div>
                                            <img class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 text-sm ease-soft-in-out h-9 w-9 rounded-xl" src="../../../assets/img/team-2.jpg" alt="avatar image">
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-0 leading-normal text-sm dark:text-white">John Michael</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">Manager</p>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="py-2.2 rounded-1.8 text-sm mr-6 inline-block whitespace-nowrap bg-transparent px-0 text-center align-baseline font-normal leading-none text-white">
                                        <i class="rounded-circle mr-1.5 inline-block h-1.5 w-1.5 bg-cyan-500 align-middle"></i>
                                        <span class="leading-tight text-xs text-slate-700 dark:text-white">positive</span>
                                    </span>
                                </td>
                                <td class="p-2 leading-normal text-center align-middle bg-transparent border-b text-sm whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">john@user.com</p>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">23/04/18</span>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">43431</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <div class="flex px-2 py-1">
                                        <div>
                                            <img class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 text-sm ease-soft-in-out h-9 w-9 rounded-xl" src="../../../assets/img/team-3.jpg" alt="avatar image">
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-0 leading-normal text-sm dark:text-white">Alexa Liras</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">Programator</p>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="py-2.2 rounded-1.8 text-sm mr-6 inline-block whitespace-nowrap bg-transparent px-0 text-center align-baseline font-normal leading-none text-white">
                                        <i class="rounded-circle mr-1.5 inline-block h-1.5 w-1.5 bg-cyan-500 align-middle"></i>
                                        <span class="leading-tight text-xs text-slate-700 dark:text-white">positive</span>
                                    </span>
                                </td>
                                <td class="p-2 leading-normal text-center align-middle bg-transparent border-b text-sm whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">alexa@user.com</p>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">11/01/19</span>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">93021</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <div class="flex px-2 py-1">
                                        <div>
                                            <img class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 text-sm ease-soft-in-out h-9 w-9 rounded-xl" src="../../../assets/img/team-4.jpg" alt="avatar image">
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-0 leading-normal text-sm dark:text-white">Laurent Perrier</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">Executive</p>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="py-2.2 rounded-1.8 text-sm mr-6 inline-block whitespace-nowrap bg-transparent px-0 text-center align-baseline font-normal leading-none text-white">
                                        <i class="rounded-circle mr-1.5 inline-block h-1.5 w-1.5 bg-slate-700 align-middle"></i>
                                        <span class="leading-tight text-xs text-slate-700 dark:text-white">neutral</span>
                                    </span>
                                </td>
                                <td class="p-2 leading-normal text-center align-middle bg-transparent border-b text-sm whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">laurent@user.com</p>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">23/04/18</span>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">10392</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <div class="flex px-2 py-1">
                                        <div>
                                            <img class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 text-sm ease-soft-in-out h-9 w-9 rounded-xl" src="../../../assets/img/team-3.jpg" alt="avatar image">
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-0 leading-normal text-sm dark:text-white">Michael Levi</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">Backend developer</p>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="py-2.2 rounded-1.8 text-sm mr-6 inline-block whitespace-nowrap bg-transparent px-0 text-center align-baseline font-normal leading-none text-white">
                                        <i class="rounded-circle mr-1.5 inline-block h-1.5 w-1.5 bg-cyan-500 align-middle"></i>
                                        <span class="leading-tight text-xs text-slate-700 dark:text-white">positive</span>
                                    </span>
                                </td>
                                <td class="p-2 leading-normal text-center align-middle bg-transparent border-b text-sm whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">michael@user.com</p>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">24/12/08</span>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">34002</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <div class="flex px-2 py-1">
                                        <div>
                                            <img class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 text-sm ease-soft-in-out h-9 w-9 rounded-xl" src="../../../assets/img/team-2.jpg" alt="avatar image">
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-0 leading-normal text-sm dark:text-white">Richard Gran</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">Manager</p>
                                </td>
                                <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="py-2.2 rounded-1.8 text-sm mr-6 inline-block whitespace-nowrap bg-transparent px-0 text-center align-baseline font-normal leading-none text-white">
                                        <i class="rounded-circle mr-1.5 inline-block h-1.5 w-1.5 bg-red-600 align-middle"></i>
                                        <span class="leading-tight text-xs text-slate-700 dark:text-white">negative</span>
                                    </span>
                                </td>
                                <td class="p-2 leading-normal text-center align-middle bg-transparent border-b text-sm whitespace-nowrap dark:border-white/40">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">richard@user.com</p>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">04/10/21</span>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap dark:border-white/40">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">91879</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 align-middle bg-transparent whitespace-nowrap">
                                    <div class="flex px-2 py-1">
                                        <div>
                                            <img class="inline-flex items-center justify-center mr-4 text-white transition-all duration-200 text-sm ease-soft-in-out h-9 w-9 rounded-xl" src="../../../assets/img/team-4.jpg" alt="avatar image">
                                        </div>
                                        <div class="flex flex-col justify-center">
                                            <h6 class="mb-0 leading-normal text-sm dark:text-white">Miriam Eric</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-2 align-middle bg-transparent whitespace-nowrap">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">Programator</p>
                                </td>
                                <td class="p-2 align-middle bg-transparent whitespace-nowrap">
                                    <span class="py-2.2 rounded-1.8 text-sm mr-6 inline-block whitespace-nowrap bg-transparent px-0 text-center align-baseline font-normal leading-none text-white">
                                        <i class="rounded-circle mr-1.5 inline-block h-1.5 w-1.5 bg-cyan-500 align-middle"></i>
                                        <span class="leading-tight text-xs text-slate-700 dark:text-white">positive</span>
                                    </span>
                                </td>
                                <td class="p-2 leading-normal text-center align-middle bg-transparent text-sm whitespace-nowrap">
                                    <p class="mb-0 leading-normal text-sm text-slate-400 dark:text-white/80">miriam@user.com</p>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent whitespace-nowrap">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">14/09/20</span>
                                </td>
                                <td class="p-2 text-center align-middle bg-transparent whitespace-nowrap">
                                    <span class="leading-normal text-sm text-slate-400 dark:text-white/80">23042</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                    </div>
                    <div class="ps__rail-y" style="top: 0px; right: 0px;">
                        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="pt-4">
        <div class="w-full px-6 mx-auto">
            <div class="flex flex-wrap items-center -mx-3 lg:justify-between">
                <div class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2 lg:flex-none">
                    <div class="leading-normal text-center copyright text-sm text-slate-500 lg:text-left">
                        ©
                        <script>
                            document.write(new Date().getFullYear() + ",");
                        </script>2022,
                        made with <i class="fa fa-heart" aria-hidden="true"></i> by
                        <a href="https://www.creative-tim.com" class="font-semibold text-slate-700 dark:text-white" target="_blank">Creative Tim</a>
                        for a better web.
                    </div>
                </div>
                <div class="w-full max-w-full px-3 mt-0 shrink-0 lg:w-1/2 lg:flex-none">
                    <ul class="flex flex-wrap justify-center pl-0 mb-0 list-none lg:justify-end">
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com" class="block px-4 pt-0 pb-1 font-normal transition-colors ease-soft-in-out text-sm text-slate-500" target="_blank">Creative Tim</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com/presentation" class="block px-4 pt-0 pb-1 font-normal transition-colors ease-soft-in-out text-sm text-slate-500" target="_blank">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://creative-tim.com/blog" class="block px-4 pt-0 pb-1 font-normal transition-colors ease-soft-in-out text-sm text-slate-500" target="_blank">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com/license" class="block px-4 pt-0 pb-1 pr-0 font-normal transition-colors ease-soft-in-out text-sm text-slate-500" target="_blank">License</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection
