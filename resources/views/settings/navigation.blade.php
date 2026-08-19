<nav class="tx-subnav" aria-label="Settings">
    <div class="tx-subnav-track">
        <x-subnav-link href="{{ route('settings.show', 'company') }}" :active="($page ?? '') == 'company'">{{ __('Company') }}</x-subnav-link>
        <x-subnav-link href="{{ route('notification') }}" :active="($page ?? '') == 'notification'">{{ __('Notification') }}</x-subnav-link>
        <x-subnav-link href="{{ route('role.index') }}" :active="($page ?? '') == 'role'">{{ __('Role') }}</x-subnav-link>
        <x-subnav-link href="{{ route('permission.index') }}" :active="request()->routeIs('permission.index')">{{ __('Menu') }}</x-subnav-link>
    </div>
</nav>
