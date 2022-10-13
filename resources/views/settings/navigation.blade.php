<nav x-data="{ open: false }" class="bg-gray-50 dark:bg-slate-900 border-b border-gray-100 dark:border-slate-600">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-6">
            <div class="flex">
                <!-- Logo -->

                <!-- Navigation Links -->
                <div class="space-x-8 sm:-my-px sm:ml-10">
                    <x-jet-nav-link href="{{ route('settings.show', 'company') }}" :active="$page == 'company'">
                        {{ __('Company') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('notification') }}" :active="$page == 'notification'">
                        {{ __('Notification') }}
                    </x-jet-nav-link>
                    <!-- <x-jet-nav-link href="{{ route('settings.show', 'omni') }}" :active="$page =='omni'">
                        {{ __('Omni-Channel API') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'chat') }}" :active="$page =='chat'">
                        {{ __('Chat') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'api') }}" :active="$page =='api'">
                        {{ __('Data API') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'ticket') }}" :active="$page =='ticket'">
                        {{ __('Ticket') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'billing') }}" :active="$page =='billing'">
                        {{ __('Billing') }}
                    </x-jet-nav-link> -->
                    <x-jet-nav-link href="{{ route('role.index') }}" :active="$page =='role'">
                        {{ __('Role') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('permission.index') }}" :active="request()->routeIs('permission.index')">
                        {{ __('Menu') }}
                    </x-jet-nav-link>
                </div>
            </div>
        </div>
    </div>

</nav>
