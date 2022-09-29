<nav x-data="{ open: false }" class="bg-white dark:bg-slate-900 dark:border-slate-600 shadow border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-6">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="space-x-8 sm:-my-px">
                    <x-jet-nav-link href="{{ route('report.show', ['billing']) }}" :active="request()->routeIs('project')">
                        {{ __('Billing') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('report.show', ['request']) }}" :active="request()->routeIs('commercial')">
                        {{ __('Log Chat') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('report.show', ['sms']) }}" :active="request()->routeIs('order')">
                        {{ __('Log SMS') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('report.show', ['log']) }}">
                        {{ __('Log API Request') }}
                    </x-jet-nav-link>
                </div>
            </div>
        </div>
    </div>

</nav>
