<nav x-data="{ open: false }" class="bg-gray-50 border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-6">
            <div class="flex">
                <!-- Logo -->

                <!-- Navigation Links -->
                <div class="space-x-8 sm:-my-px sm:ml-10">
                    <x-jet-nav-link href="{{ route('settings.show', 'omni') }}" :active="request()->routeIs('settings.show', 'omni')">
                        {{ __('Omni-Channel API') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'chat') }}" :active="request()->routeIs('settings.show', 'chat')">
                        {{ __('Chat') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'api') }}" :active="request()->routeIs('settings.show', 'api')">
                        {{ __('Data API') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'ticket') }}" :active="request()->routeIs('settings.show', 'ticket')">
                        {{ __('Ticket') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('settings.show', 'billing') }}" :active="request()->routeIs('settings.show', 'billing')">
                        {{ __('Billing') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('role.index') }}" :active="request()->routeIs('role.index')">
                        {{ __('Role') }}
                    </x-jet-nav-link>
                </div>
            </div>
        </div>
    </div>

</nav>
