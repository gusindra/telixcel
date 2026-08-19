<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <x-page-section>
        <div x-data="{ ptab: 'profile' }" class="md:flex md:gap-8">

            {{-- Left sidebar (Profile / Credential / Team) --}}
            <aside class="md:w-56 flex-shrink-0 mb-6 md:mb-0">
                @php
                    $navItem = "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition focus:outline-none";
                    $navOn   = "bg-blue-50 dark:bg-blue-900/30 text-blue-600";
                    $navOff  = "text-gray-500 dark:text-slate-400 hover:bg-gray-50 dark:hover:bg-slate-700/40 hover:text-gray-700 dark:hover:text-slate-200";
                @endphp
                <nav class="space-y-1">
                    <button type="button" @click="ptab = 'profile'" :class="ptab === 'profile' ? '{{ $navOn }}' : '{{ $navOff }}'" class="{{ $navItem }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('Profile') }}
                    </button>
                    <button type="button" @click="ptab = 'credential'" :class="ptab === 'credential' ? '{{ $navOn }}' : '{{ $navOff }}'" class="{{ $navItem }}">
                        <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ __('Credential') }}
                    </button>
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <button type="button" @click="ptab = 'team'" :class="ptab === 'team' ? '{{ $navOn }}' : '{{ $navOff }}'" class="{{ $navItem }}">
                            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a3 3 0 10-3-3"/></svg>
                            {{ __('Team') }}
                        </button>
                    @endif
                </nav>
            </aside>

            {{-- Right content --}}
            <div class="flex-1 min-w-0">

                {{-- Profile --}}
                <div x-show="ptab === 'profile'">
                    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                        @livewire('profile.update-profile-information-form')
                    @endif
                </div>

                {{-- Credential: password, 2FA, sessions, delete --}}
                <div x-show="ptab === 'credential'" x-cloak class="space-y-6" style="display:none;">
                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                        @livewire('profile.update-password-form')
                    @endif

                    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                        <x-jet-section-border />
                        @livewire('profile.two-factor-authentication-form')
                    @endif

                    <x-jet-section-border />
                    @livewire('profile.logout-other-browser-sessions-form')

                    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                        <x-jet-section-border />
                        @livewire('profile.delete-user-form')
                    @endif
                </div>

                {{-- Team --}}
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div x-show="ptab === 'team'" x-cloak style="display:none;">
                        <div class="bg-white dark:bg-slate-800 shadow sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-slate-200">{{ __('Team') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">
                                {{ __('Manage your team, members and invitations.') }}
                            </p>

                            @if (Auth::user()->currentTeam)
                                <div class="mt-4 flex items-center gap-3">
                                    <span class="flex items-center justify-center h-10 w-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 font-bold uppercase">
                                        {{ Str::substr(Auth::user()->currentTeam->name, 0, 1) }}
                                    </span>
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-slate-200">{{ Auth::user()->currentTeam->name }}</p>
                                        <p class="text-xs text-gray-400">{{ __('Current Team') }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-6 flex flex-wrap gap-3">
                                @if (Auth::user()->currentTeam)
                                    <a href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                                       class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-slate-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition">
                                        {{ __('Team Settings') }}
                                    </a>
                                @endif
                                <a href="{{ route('teams.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-md font-semibold text-xs text-gray-700 dark:text-slate-200 uppercase tracking-widest hover:bg-gray-50 transition">
                                    {{ __('Create New Team') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </x-page-section>
</x-app-layout>
