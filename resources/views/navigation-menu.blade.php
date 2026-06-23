<nav x-data="{ open: false }" class="bg-white dark:text-white border-b border-gray-100 dark:border-slate-50/[0.06] supports-backdrop-blur:bg-white/60 dark:bg-slate-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a class="navbar-brand" href="/">
                        <img class="dark:bg-white" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/logo-150.png" title="{{ env('APP_NAME')}}" style="width: 150px;"/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-jet-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-jet-nav-link>

                    {{-- Main menu — consistent across all pages (no more swapping) --}}
                    @if (@Auth::user()->role || Auth::user()->super->first())
                        @if((Auth::user()->activeRole && str_contains(Auth::user()->activeRole->role->name, "Admin")))
                            <x-jet-nav-link href="{{ route('user.index') }}" :active="request()->routeIs('user.index')">
                                {{ __('Users') }}
                            </x-jet-nav-link>
                            <x-jet-nav-link href="{{ route('billing') }}" :active="request()->routeIs('billing')">
                                {{ __('Billing') }}
                            </x-jet-nav-link>
                            <x-jet-nav-link href="{{ route('agent') }}" :active="request()->routeIs('agent')">
                                {{ __('AI Console') }}
                            </x-jet-nav-link>
                        @endif
                    @endif

                    <x-jet-nav-link href="{{ route('ticket') }}" :active="request()->routeIs('ticket')">
                        {{ __('Ticket') }}
                    </x-jet-nav-link>

                    @if (Auth::user()->activeRole)
                        <x-jet-nav-link href="{{ route('assistant') }}"
                            :active="request()->routeIs('assistant') || request()->routeIs('project*') || request()->routeIs('commercial*') || request()->routeIs('order') || request()->routeIs('show.order') || request()->routeIs('show.invoice') || request()->routeIs('invoice') || request()->routeIs('commission*')">
                            {{ __('Assistant') }}
                        </x-jet-nav-link>
                    @else
                        <x-jet-nav-link href="{{ route('client') }}" :active="request()->routeIs('client')">
                            {{ __('Customers') }}
                        </x-jet-nav-link>
                        <x-jet-nav-link href="{{ route('template') }}" :active="request()->routeIs('template')">
                            {{ __('Templates') }}
                        </x-jet-nav-link>
                        @if ( Auth::user()->currentTeam && Auth::user()->currentTeam->user_id == Auth::user()->id )
                            <x-jet-nav-link href="{{ route('billing') }}" :active="request()->routeIs('billing')">
                                {{ __('Report') }}
                            </x-jet-nav-link>
                        @endif
                    @endif

                    @if((Auth::user()->activeRole && str_contains(Auth::user()->activeRole->role->name, "Super Admin")))
                        <x-jet-nav-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">
                            {{ __('Settings') }}
                        </x-jet-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6 flex-auto justify-end space-x-1">

                <!-- Teams Dropdown -->
                @if (Auth::user()->currentTeam && Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ml-3 relative">
                        <x-jet-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-slate-300 bg-white supports-backdrop-blur:bg-white/60 dark:bg-slate-800 hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    @if (Auth::user()->currentTeam->id !== 1)
                                        <!-- Team Settings -->
                                        <x-jet-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                            {{ __('Team Settings') }}
                                        </x-jet-dropdown-link>
                                    @endif

                                    <!-- @if (Auth::user()->hasTeamRole(Auth::user()->currentTeam, 'admin') || @Auth::user()->isSuper->role=='superadmin') -->
                                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                            <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                                {{ __('Create New Team') }}
                                            </x-jet-dropdown-link>
                                        @endcan
                                    <!-- @endif -->

                                    <div class="border-t border-gray-100"></div>

                                    @livewire('switch-team')
                                </div>
                            </x-slot>
                        </x-jet-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-jet-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex items-center text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 dark:bg-slate-700 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    @if(Auth::user()->activeRole)
                                    <span class="ml-2 text-xs whitespace-nowrap">{{Auth::user()->activeRole->role->name}}</span>
                                    @endif
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                        {{ Auth::user()->name }}

                                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            @livewire('switch-role')

                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-jet-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-jet-dropdown-link>
                            @if(balance(auth()->user())>0)
                            <x-jet-dropdown-link href="{{ route('payment.deposit') }}" class="flex justify-between">
                                <span>{{ __('Balance') }}</span> <small>Rp {{number_format(balance(auth()->user()))}}</small>
                            </x-jet-dropdown-link>
                            @endif
                            @if (Auth::user()->hasTeamRole(Auth::user()->currentTeam, 'admin'))
                                @if (auth()->user()->currentTeam && Laravel\Jetstream\Jetstream::hasApiFeatures() && auth()->user()->currentTeam->id != 1)
                                    <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                        {{ __('API Tokens') }}
                                    </x-jet-dropdown-link>
                                @endif
                            @endif
                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                    {{ __('Create New Team') }}
                                </x-jet-dropdown-link>
                            @endcan
                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-jet-dropdown-link href="{{ route('logout') }}"
                                         onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-jet-dropdown-link>
                            </form>
                        </x-slot>
                    </x-jet-dropdown>
                </div>

                <!-- Language Switcher -->
                <div class="ml-3 relative">
                    <x-jet-dropdown align="right" width="40">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-2 py-1.5 rounded-md text-sm text-gray-500 dark:text-slate-300 hover:text-gray-700 dark:hover:text-white focus:outline-none transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                    <circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>
                                </svg>
                                <span class="ml-1 uppercase font-semibold text-xs">{{ app()->getLocale() }}</span>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <a href="{{ url('/lang/en') }}" class="flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-slate-700 {{ app()->getLocale()=='en' ? 'font-semibold text-blue-600' : 'text-gray-600 dark:text-slate-300' }}">
                                English
                                @if(app()->getLocale()=='en')<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </a>
                            <a href="{{ url('/lang/id') }}" class="flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-slate-700 {{ app()->getLocale()=='id' ? 'font-semibold text-blue-600' : 'text-gray-600 dark:text-slate-300' }}">
                                Indonesia
                                @if(app()->getLocale()=='id')<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>@endif
                            </a>
                        </x-slot>
                    </x-jet-dropdown>
                </div>

                <!-- Status Dropdown -->
                <div class="ml-3 relative">
                    @livewire('agent-status')
                </div>

                <!-- Notification Dropdown -->
                @livewire('notification-app', ['client_id' => Auth::user()->id], key(Auth::user()->id))

                @if(auth()->user()->currentTeam && auth()->user()->currentTeam->id == env('IN_HOUSE_TEAM_ID'))
                    <!-- Global Search -->
                    @livewire('search.all')
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-jet-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-jet-responsive-nav-link>
            <x-jet-responsive-nav-link href="{{ route('ticket') }}" :active="request()->routeIs('ticket')">
                {{ __('Ticket') }}
            </x-jet-responsive-nav-link>
            @if (Auth::user()->hasTeamRole(Auth::user()->currentTeam, 'admin'))
                <x-jet-responsive-nav-link href="{{ route('client') }}" :active="request()->routeIs('client')">
                    {{ __('Customers') }}
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ route('template') }}" :active="request()->routeIs('template')">
                    {{ __('Templates') }}
                </x-jet-responsive-nav-link>
                @if ( Auth::user()->currentTeam && Auth::user()->currentTeam->user_id == Auth::user()->id )
                <x-jet-responsive-nav-link href="{{ route('billing') }}" :active="request()->routeIs('billing')">
                    {{ __('Billing') }}
                </x-jet-responsive-nav-link>
                @endif
                <x-jet-responsive-nav-link href="{{ route('agent') }}" :active="request()->routeIs('agent')">
                    {{ __('AI Console') }}
                </x-jet-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="flex-shrink-0 mr-3">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-jet-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-jet-responsive-nav-link>

                <!-- Language -->
                <div class="border-t border-gray-200 dark:border-slate-700"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Language') }}</div>
                <x-jet-responsive-nav-link href="{{ url('/lang/en') }}" :active="app()->getLocale()=='en'">
                    English
                </x-jet-responsive-nav-link>
                <x-jet-responsive-nav-link href="{{ url('/lang/id') }}" :active="app()->getLocale()=='id'">
                    Indonesia
                </x-jet-responsive-nav-link>
                <div class="border-t border-gray-200 dark:border-slate-700"></div>

                @if (auth()->user()->currentTeam && Laravel\Jetstream\Jetstream::hasApiFeatures() && auth()->user()->currentTeam->id != 1)
                    <x-jet-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-jet-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-jet-responsive-nav-link href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-jet-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Auth::user()->currentTeam && Laravel\Jetstream\Jetstream::hasTeamFeatures() )
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-jet-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-jet-responsive-nav-link>

                    @if(auth()->user()->super->first() && auth()->user()->super->first()->role == 'member')
                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <x-jet-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                                {{ __('Create New Team') }}
                            </x-jet-responsive-nav-link>
                        @endcan
                    @endif

                    <div class="border-t border-gray-200"></div>

                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Switch Teams') }}
                    </div>

                    @foreach (Auth::user()->allTeams() as $team)
                        <x-jet-switchable-team :team="$team" component="jet-responsive-nav-link" />
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Assistant sub-menu (second layer) — only inside the Assistant workspace.
         Commercial & Order here are the GLOBAL lists (across all projects); the same-named
         tabs inside a project detail are scoped to that single project. --}}
    @php
        $inAssistant = request()->routeIs('assistant') || request()->routeIs('project*')
            || request()->routeIs('commercial*') || request()->routeIs('order')
            || request()->routeIs('show.order') || request()->routeIs('show.invoice')
            || request()->routeIs('commission*') || request()->routeIs('invoice');
        $isProject = request()->routeIs('project') || request()->routeIs('project.show');
        $isCommercial = request()->routeIs('commercial') || request()->routeIs('commercial.show') || request()->routeIs('commercial.edit.show');
        $isOrder = request()->routeIs('order') || request()->routeIs('show.order') || request()->routeIs('invoice') || request()->routeIs('show.invoice') || request()->routeIs('commission*');
    @endphp
    @if($inAssistant)
        <div class="bg-gray-50 dark:bg-slate-900 border-b border-gray-200 dark:border-slate-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex h-12 items-center space-x-6">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-slate-500">{{ __('Assistant') }}</span>
                    <a href="{{ route('project') }}"
                       class="text-sm font-medium pb-px border-b-2 transition {{ $isProject ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-slate-400' }}">
                        {{ __('Project') }}
                    </a>
                    <a href="{{ route('commercial') }}"
                       class="text-sm font-medium pb-px border-b-2 transition {{ $isCommercial ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-slate-400' }}">
                        {{ __('Commercial') }}
                    </a>
                    <a href="{{ route('order') }}"
                       class="text-sm font-medium pb-px border-b-2 transition {{ $isOrder ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-slate-400' }}">
                        {{ __('Order') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
</nav>
