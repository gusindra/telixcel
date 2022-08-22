<nav x-data="{ open: false }" class="bg-gray-50 border-b border-gray-100 dark:bg-slate-900 dark:border-slate-600 shadow">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-6">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="space-x-8 sm:-my-px">
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
