<nav class="tx-subnav" aria-label="AI">
    <div class="tx-subnav-track">
        <x-subnav-link href="{{ route('ai.applications') }}" :active="request()->routeIs('ai.applications') || request()->routeIs('ai.applications.*')">{{ __('Applications') }}</x-subnav-link>
        <x-subnav-link href="{{ route('ai.usage') }}" :active="request()->routeIs('ai.usage')">{{ __('Usage') }}</x-subnav-link>
        <x-subnav-link href="{{ route('ai.logs') }}" :active="request()->routeIs('ai.logs')">{{ __('Log') }}</x-subnav-link>
        <x-subnav-link href="{{ route('ai.docs') }}" :active="request()->routeIs('ai.docs') || request()->routeIs('ai.docs.spec')">{{ __('API Docs') }}</x-subnav-link>
        <x-subnav-link href="{{ route('ai.settings') }}" :active="request()->routeIs('ai.settings')">{{ __('Settings') }}</x-subnav-link>
    </div>
</nav>
