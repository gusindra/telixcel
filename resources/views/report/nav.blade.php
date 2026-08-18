<nav class="tx-subnav" aria-label="Report">
    <div class="tx-subnav-track">
        <x-subnav-link href="{{ route('report.show', ['billing']) }}" :active="request()->is('billing') || request()->is('report/billing')">{{ __('Billing') }}</x-subnav-link>
        <x-subnav-link href="{{ route('report.show', ['request']) }}" :active="request()->is('report/request')">{{ __('Log Chat') }}</x-subnav-link>
        <x-subnav-link href="{{ route('report.show', ['sms']) }}" :active="request()->is('report/sms')">{{ __('Log SMS') }}</x-subnav-link>
        <x-subnav-link href="{{ route('report.show', ['log']) }}" :active="request()->is('report/log')">{{ __('Log API Request') }}</x-subnav-link>
    </div>
</nav>
