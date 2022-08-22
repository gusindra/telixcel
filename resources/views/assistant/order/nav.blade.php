<nav x-data="{ open: false }" class="bg-gray-50 dark:bg-slate-900 dark:border-slate-600 border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto py-2">
        <div class="flex justify-between h-6">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="space-x-8 sm:-my-px sm:ml-10">
                    <x-jet-nav-link href="{{ route('order') }}" :active="request()->routeIs('order')">
                        {{__('Order')}}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('invoice') }}" :active="request()->routeIs('invoice')">
                        {{__('Invoice')}}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('commission') }}" :active="request()->routeIs('commission')">
                        {{__('Commissions')}}
                    </x-jet-nav-link>
                </div>
            </div>
        </div>
    </div>
</nav>
