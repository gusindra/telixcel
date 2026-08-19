<nav class="tx-subnav" aria-label="{{ $application->name }}">
    <div class="tx-subnav-track">
        <x-subnav-link href="{{ route('ai.applications.show', $application) }}" :active="$section === 'settings'">{{ __('Settings') }}</x-subnav-link>
        <x-subnav-link href="{{ route('ai.applications.usage', $application) }}" :active="$section === 'usage'">{{ __('Usage') }}</x-subnav-link>
        <x-subnav-link href="{{ route('ai.applications.requests', $application) }}" :active="$section === 'requests'">{{ __('Requests') }}</x-subnav-link>
        <x-subnav-link href="{{ route('ai.applications.test', $application) }}" :active="$section === 'test'">{{ __('Test') }}</x-subnav-link>
    </div>
    <div class="tx-subnav-meta" title="{{ $application->name }}">
        <span class="tx-subnav-meta-name">{{ $application->name }}</span>
    </div>
</nav>
