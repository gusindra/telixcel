<div>
    @if($project->status=='draft')
        <div class="flex flex-wrap items-center mb-4 -mx-3">
            <div class="w-9/12 max-w-full px-3 flex-0">
                <h5 class="z-10 mb-1 text-transparent bg-clip-text bg-gradient-to-tl from-purple-700 to-pink-500">
                    <a href="javascript:;" class="text-transparent">Submission Process</a>
                </h5>
            </div>
        </div>
        <div class="relative flex flex-col min-w-0 mt-6 break-words bg-white border-0 dark:bg-gray-950 dark:shadow-soft-dark-xl shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="flex-auto p-4">
                <div class="flex">
                    <div class="text-white inline-flex items-center justify-center text-sm rounded-xl h-14 w-14 transition-all duration-200 ease-soft-in-out">
                        <img class="w-full" src="https://ui-avatars.com/api/?name={{auth()->user()->name}}&amp;color=7F9CF5&amp;background=EBF4FF" alt="Image placeholder">
                    </div>
                    <div class="my-auto ml-2">
                        <h6 class="mb-0 dark:text-white">{{auth()->user()->name}}</h6>
                        <p class="mb-0 leading-tight text-xs dark:text-white/60">{{auth()->user()->activeRole?auth()->user()->activeRole->role->name:''}}</p>
                    </div>
                </div>
                <!-- <p class="mt-4 dark:text-white/60"> You have an upcoming video call for
                    <span class="text-fuchsia-500">Soft Design</span>
                    at 5:00 PM.
                </p>
                <p class="mb-0 dark:text-white/60">
                    <b>Meeting ID:</b>
                    111-968-981
                </p> -->
                <hr class="h-px mx-0 my-4 bg-transparent border-0 opacity-25 bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent">
                <div class="flex">
                    <button wire:click="submit" type="button" class="inline-block px-8 py-2 m-0 mb-0 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer text-xs tracking-tight-soft ease-soft-in leading-pro bg-gradient-to-tl from-green-600 to-lime-400 shadow-soft-md bg-150 bg-x-25 hover:scale-102 active:opacity-85">{{ $approvals->count() > 0 ? __('Re-Submit') : __('Submit') }}</button>
                </div>
            </div>
        </div>
    @endif
    @if($approvals->count() > 0)
        <div class="flex flex-wrap items-center mb-4 -mx-3">
            <div class="w-9/12 max-w-full px-3 flex-0">
                <h5 class="z-10 mb-1 text-transparent bg-clip-text bg-gradient-to-tl from-purple-700 to-pink-500">
                    <a href="javascript:;" class="text-transparent">Track Progress</a>
                </h5>
            </div>
        </div>

        <!-- The offcanvas component -->
        <div class="block sm:hidden" x-data="{ offcanvas: false }">
            <button class="fixed top-52 right-0 bg-blue-100  p-1 text-sm text-gray-400" @click="offcanvas = true">Approval</button>
            <section x-show="offcanvas" class="fixed inset-y-0 right-0 z-50 flex">
                <div class="w-60 max-w-sm">
                    <div class="flex flex-col h-full divide-y divide-gray-200 bg-gray-100">
                        <div class="overflow-y-scroll">
                            <header class="flex items-center justify-between h-16 pl-6">

                                <button aria-label="Close menu" class="w-16 h-16 border-l border-gray-200" type="button" @click="offcanvas = false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </header>

                            <nav class="flex flex-col text-sm font-medium text-gray-500 border-t border-b border-gray-200 divide-y divide-gray-200">
                                <div class="p-8">
                                    <ol class="relative">
                                        @foreach ($approvals as $approval)
                                        <li class=" {{$approval->status!=null ? 'border-l-2 border-blue-600':'border-gray-300'}} pl-6 pb-4">
                                            @if($approval->status=='decline')
                                            <div class="absolute w-6 h-6 bg-yellow-600 rounded-full -left-3 border border-white dark:border-blue-900 dark:bg-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="m-auto mt-1 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </div>
                                            @elseif($approval->status!='submited')
                                            <div class="absolute w-6 h-6 {{$approval->status=='approved' ? 'bg-blue-600':'bg-gray-300'}} rounded-full -left-3 border border-white dark:border-blue-900 dark:bg-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="m-auto mt-1 h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                </svg>
                                            </div>
                                            @else
                                            <div class="absolute w-6 h-6 bg-blue-600 rounded-full -left-3 border border-white dark:border-blue-900 dark:bg-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="m-auto mt-1 h-4 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                                </svg>
                                            </div>
                                            @endif
                                            <div class="flex gap-2">
                                                <h3 class="mt-0 text-sm font-semibold text-gray-900 dark:text-white">{{$approval->status!='submited' ? $approval->role->name : $approval->user->name}}</h3>
                                                <p class="mt-0 text-sm font-semibolde text-gray-900 dark:text-gray-500 capitalize">{{$approval->status}}</p>
                                            </div>
                                            @if($approval->status!=null)
                                            <div class="flex justify-end">
                                                @if($approval->user_id)
                                                <p class="mb-2 mt-2 text-xs font-bold leading-none text-gray-400 dark:text-gray-500 flex justify-between">
                                                    <img class="h-4 w-4 rounded-full object-cover" src="{{ $approval->user->profile_photo_url }}" alt="{{ $approval->user->name }}" />
                                                    <span class="mx-1">{{$approval->user_id ? $approval->user->name : ''}}</span>
                                                </p>
                                                @endif
                                                <p class="text-right flex justify-around">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-400 mt-2 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <time class="mb-3 mt-2 text-xs font-thin leading-none text-gray-400 dark:text-gray-500">{{$approval->updated_at->format('dM-y H:i')}}</time>
                                                </p>
                                            </div>
                                            @if($approval->comment!='')
                                            <p class="text-xs font-normal text-gray-500 dark:text-gray-400 bg-gray-300 rounded-sm px-2 py-1">{{$approval->comment}}</p>
                                            @endif
                                            @endif
                                        </li>
                                        @endforeach
                                        @if($approval->status!=null)
                                        <li class="ml-5 mb-6">
                                            <div class="absolute w-6 h-6 {{$approval->status=='approved' ? 'bg-blue-600':'bg-gray-300'}} rounded-full -left-3 border border-white dark:border-blue-900 dark:bg-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="m-auto mt-1 h-3 w-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notified</h3>
                                                <ul class="text-left">
                                                    @foreach ($approvals->groupBy('user_id') as $approval)
                                                    <li class="mb-2 mt-1 text-xs font-normal leading-none text-gray-400 dark:text-gray-300 capitalize">{{$approval[0]->user->name}}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </li>
                                        @endif
                                    </ol>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endif
    @if($project->status=='submit' && $approval && $project->approval && checkRoles($project->approval->role_id))
        <div class="px-4 py-5 bg-white dark:bg-slate-600 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md mt-4">
            <div class="sm:px-0">
                <h3 class="text-base font-medium text-gray-900 dark:text-slate-300">Next Process</h3>
            </div>
            <div class="col-span-12 sm:col-span-1">
                <x-jet-label for="remark" value="{{ __('Remark') }}" />
                <x-jet-input id="remark" type="text" class="mt-1 block w-full text-xs" wire:model="remark" />
                <x-jet-input-error for="remark" class="mt-2" />
            </div>
            <div class="w-auto text-center mt-4 flex gap-2">
                <button type="submit" wire:click="decline" class="inline-flex items-center px-2 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition dark:bg-red-400 hover:bg-red-400 bg-red-200" wire:click="submit">
                    {{ __('Decline') }}
                </button>
                <x-jet-button wire:click="next" class="hover:bg-green-700 bg-green-500 px-2">
                    {{ __('Approve') }}
                </x-jet-button>
            </div>
        </div>
    @endif

    <div>
        <div class="relative before:absolute before:left-4 before:top-0 before:h-full before:border-r-2 before:border-solid before:border-slate-100 before:content-[''] lg:before:-ml-px">
            @foreach ($approvals as $approval)
                <div class="relative mb-4">
                    @if($approval->status=='decline')
                        <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-yellow-700 text-center font-semibold">
                            <i class="relative text-transparent ni leading-none ni-bell-55 bg-gradient-to-tl from-yellow-600 to-lime-400 leading-pro z-1 bg-clip-text"></i>
                        </span>
                    @elseif(!in_array($approval->status, ['submited', 'approved']))
                        <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                            <i class="relative text-transparent ni leading-none ni-bell-55 bg-gradient-to-tl from-gray-600 to-lime-400 leading-pro z-1 bg-clip-text"></i>
                        </span>
                    @else
                        <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-green-600 text-center font-semibold">
                            <i class="relative text-transparent ni leading-none ni-bell-55 bg-gradient-to-tl from-blue-600 to-lime-400 leading-pro z-1 bg-clip-text"></i>
                        </span>
                    @endif
                    <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                        <h6 class="mb-0 font-semibold leading-normal dark:text-white dark:text-slate-200 text-sm">{{$approval->status!='submited' ? $approval->role->name : $approval->user->name}} {{$approval->description}}</h6>
                        <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">{{$approval->updated_at->format('d M y g:i A')}}</p>
                        <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">{{$approval->comment}}</p>
                        <!-- <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-green-600 to-lime-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">{{$approval->status}}</span> -->
                        <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-blue-600 to-cyan-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">{{$approval->status}}</span>
                    </div>
                </div>
            @endforeach

            @if(@$approval->status!=null)
                <div class="relative mb-4">
                    <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                        <i class="relative text-transparent ni leading-none ni-html5 bg-gradient-to-tl from-red-600 to-rose-400 leading-pro z-1 bg-clip-text"></i>
                    </span>
                    <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                        <h6 class="mb-0 font-semibold leading-normal text-white text-sm">Notified</h6>
                        <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">{{$approval->updated_at->format('d M y g:i A')}}</p>
                        @foreach ($approvals as $approval)
                        <span class="rounded-1.8 text-sm mt-4 block whitespace-nowrap bg-transparent px-0 pb-0 text-left align-baseline font-normal leading-none text-white">
                            <i class="bg-gradient-to-tl from-blue-600 to-cyan-400 rounded-circle mr-1.5 inline-block h-1.5 w-1.5 align-middle"></i>
                            <span class="font-semibold leading-tight text-xs text-slate-500 dark:text-white">{{$approval->user->name}}</span>
                        </span>
                        @endforeach
                    </div>
                </div>
            @endif
            <!-- <div class="relative mb-4">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-html5 bg-gradient-to-tl from-red-600 to-rose-400 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">New order #1832412</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">21 DEC 11 PM</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-red-600 to-rose-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">order</span>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-red-600 to-rose-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">#1832412</span>
                </div>
            </div>
            <div class="relative mb-4">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-cart bg-gradient-to-tl from-blue-600 to-cyan-400 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">Server payments for April</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">21 DEC 9:34 PM</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-blue-600 to-cyan-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">server</span>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-blue-600 to-cyan-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">payments</span>
                </div>
            </div>
            <div class="relative mb-4">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-credit-card bg-gradient-to-tl from-red-500 to-yellow-400 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">New card added for order #4395133</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">20 DEC 2:20 AM</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-red-500 to-yellow-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">card</span>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-red-500 to-yellow-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">#4395133</span>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-red-500 to-yellow-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">priority</span>
                </div>
            </div>
            <div class="relative mb-4">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-key-25 bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">Unlock packages for development</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">18 DEC 4:54 AM</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-purple-700 to-pink-500 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">development</span>
                </div>
            </div>
            <div class="relative mb-4">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-archive-2 bg-gradient-to-tl from-green-600 to-lime-400 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">New message unread</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">16 DEC</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-green-600 to-lime-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">message</span>
                </div>
            </div>
            <div class="relative mb-4">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-check-bold bg-gradient-to-tl from-blue-600 to-cyan-400 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">Notifications unread</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">15 DEC</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-blue-600 to-cyan-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">notification</span>
                </div>
            </div>
            <div class="relative mb-4">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-box-2 bg-gradient-to-tl from-red-500 to-yellow-400 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">New request</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">14 DEC</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-red-500 to-yellow-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">request</span>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-red-500 to-yellow-400 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">priority</span>
                </div>
            </div>
            <div class="relative mb-0">
                <span class="w-6.5 h-6.5 rounded-circle text-base z-1 absolute left-4 inline-flex -translate-x-1/2 items-center justify-center bg-slate-700 text-center font-semibold">
                    <i class="relative text-transparent ni leading-none ni-controller bg-gradient-to-tl from-gray-900 to-slate-800 leading-pro z-1 bg-clip-text"></i>
                </span>
                <div class="ml-12 pt-1.4 max-w-120 relative -top-1.5 w-auto">
                    <h6 class="mb-0 font-semibold leading-normal text-white text-sm">Controller issues</h6>
                    <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-slate-400">13 DEC</p>
                    <p class="mt-4 mb-2 leading-normal text-sm text-slate-400">People care about how you see the world, how you think, what motivates you, what you’re struggling with or afraid of.</p>
                    <span class="py-1.8 px-3 text-xxs rounded-1 bg-gradient-to-tl from-gray-900 to-slate-800 inline-block whitespace-nowrap text-center align-baseline font-bold uppercase leading-none text-white">controller</span>
                </div>
            </div> -->
        </div>
    </div>
</div>
