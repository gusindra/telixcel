<header class="tx-header">
    <button
        type="button"
        class="tx-iconbtn tx-nav-toggle h-9 w-9 rounded-lg"
        @click="sidebarOpen = !sidebarOpen"
        :aria-label="sidebarOpen ? '{{ __('Close menu') }}' : '{{ __('Open menu') }}'"
    >
        <span class="material-symbols-outlined" x-text="sidebarOpen ? 'close' : 'menu'">menu</span>
    </button>

    <div class="tx-crumb-slot">
        <x-breadcrumb />
    </div>

    <div class="flex items-center gap-1">
        <button
            type="button"
            x-cloak
            x-on:click="darkMode = !darkMode"
            class="tx-iconbtn h-9 w-9 rounded-lg"
            aria-label="Toggle theme"
        >
            <span class="material-symbols-outlined" x-show="!darkMode">dark_mode</span>
            <span class="material-symbols-outlined" x-show="darkMode">light_mode</span>
        </button>

        @if (Auth::user()->currentTeam && Laravel\Jetstream\Jetstream::hasTeamFeatures())
            <div class="relative">
                <x-jet-dropdown align="right" width="60">
                    <x-slot name="trigger">
                        <button type="button" class="tx-iconbtn tx-13 tx-team-name inline-flex items-center h-9 px-2.5 rounded-lg font-medium">
                            <span class="truncate">{{ Auth::user()->currentTeam->name }}</span>
                            <span class="material-symbols-outlined ml-0.5" style="font-size:18px;">expand_more</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="w-60">
                            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Team') }}</div>
                            @if (Auth::user()->currentTeam->id !== 1)
                                <x-jet-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                    {{ __('Team Settings') }}
                                </x-jet-dropdown-link>
                            @endif
                            @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                <x-jet-dropdown-link href="{{ route('teams.create') }}">
                                    {{ __('Create New Team') }}
                                </x-jet-dropdown-link>
                            @endcan
                            <div class="border-t border-gray-100"></div>
                            @livewire('switch-team')
                        </div>
                    </x-slot>
                </x-jet-dropdown>
            </div>
        @endif

        <div class="relative hidden sm:block">
            <x-jet-dropdown align="right" width="40">
                <x-slot name="trigger">
                    <button class="tx-iconbtn tx-11 inline-flex items-center justify-center h-9 px-2.5 rounded-lg font-semibold uppercase tracking-wide">
                        {{ app()->getLocale() }}
                    </button>
                </x-slot>
                <x-slot name="content">
                    <a href="{{ url('/lang/en') }}" class="flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-slate-700 {{ app()->getLocale()=='en' ? 'font-semibold text-gray-900' : 'text-gray-600 dark:text-slate-300' }}">
                        English
                    </a>
                    <a href="{{ url('/lang/id') }}" class="flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-slate-700 {{ app()->getLocale()=='id' ? 'font-semibold text-gray-900' : 'text-gray-600 dark:text-slate-300' }}">
                        Indonesia
                    </a>
                </x-slot>
            </x-jet-dropdown>
        </div>

        <div class="relative">
            @livewire('agent-status')
        </div>

        @livewire('notification-app', ['client_id' => Auth::user()->id], key(Auth::user()->id))

        @if (auth()->user()->currentTeam && auth()->user()->currentTeam->id == env('IN_HOUSE_TEAM_ID'))
            <button type="button" class="tx-iconbtn h-9 w-9 rounded-lg" onclick="window.livewire.emit('openGlobalSearch')" aria-label="{{ __('Search') }}">
                <span class="material-symbols-outlined">search</span>
            </button>
        @endif

        <span class="tx-divider mx-1.5 hidden sm:block"></span>

        <div class="relative">
            <x-jet-dropdown align="right" width="48">
                <x-slot name="trigger">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <button class="tx-focus tx-avatar flex items-center h-8 w-8 focus:outline-none">
                            <img class="h-full w-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </button>
                    @else
                        <button type="button" class="tx-iconbtn tx-13 inline-flex h-9 items-center px-2.5 rounded-lg font-medium">
                            {{ Auth::user()->name }}
                        </button>
                    @endif
                </x-slot>
                <x-slot name="content">
                    @livewire('switch-role')
                    <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Account') }}</div>
                    <x-jet-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-jet-dropdown-link>
                    @if (balance(auth()->user()) > 0)
                        <x-jet-dropdown-link href="{{ route('payment.deposit') }}" class="flex justify-between">
                            <span>{{ __('Balance') }}</span>
                            <small>Rp {{ number_format(balance(auth()->user())) }}</small>
                        </x-jet-dropdown-link>
                    @endif
                    @if (Auth::user()->hasTeamRole(Auth::user()->currentTeam, 'admin'))
                        @if (auth()->user()->currentTeam && Laravel\Jetstream\Jetstream::hasApiFeatures() && auth()->user()->currentTeam->id != 1)
                            <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">{{ __('API Tokens') }}</x-jet-dropdown-link>
                        @endif
                    @endif
                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-jet-dropdown-link href="{{ route('teams.create') }}">{{ __('Create New Team') }}</x-jet-dropdown-link>
                    @endcan
                    <div class="border-t border-gray-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-jet-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-jet-dropdown-link>
                    </form>
                </x-slot>
            </x-jet-dropdown>
        </div>
    </div>
</header>
