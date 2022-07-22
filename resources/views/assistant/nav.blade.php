<nav x-data="{ open: false }" class="bg-gray-50 border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-6">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="space-x-8 sm:-my-px sm:ml-10">
                    <!-- <x-jet-nav-link href="{{ route('project') }}" :active="request()->routeIs('project')">
                        {{ __('Project') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('commercial') }}" :active="request()->routeIs('commercial')">
                        {{ __('Commercial') }}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('order') }}" :active="request()->routeIs('order')">
                        {{ __('Order') }}
                    </x-jet-nav-link> -->
                    <x-jet-nav-link href="{{ route('commercial.show', ['item']) }}" :active="$key=='item'">
                        {{__('Product Master Data')}}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('commercial.show', ['quotation']) }}" :active="$key=='quotation'">
                        {{__('Quotation')}}
                    </x-jet-nav-link>
                    <x-jet-nav-link href="{{ route('commercial.show', ['contract']) }}" :active="$key=='contract'">
                        {{__('Contract')}}
                    </x-jet-nav-link>
                </div>
            </div>
        </div>
    </div>
</nav>
