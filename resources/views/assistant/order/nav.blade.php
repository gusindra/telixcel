<nav class="tx-subnav" aria-label="Order">
    <div class="tx-subnav-track">
        <x-subnav-link href="{{ route('order') }}" :active="request()->routeIs('order')">{{ __('Order') }}</x-subnav-link>
        <x-subnav-link href="{{ route('invoice') }}" :active="request()->routeIs('invoice')">{{ __('Invoice') }}</x-subnav-link>
        <x-subnav-link href="{{ route('commission') }}" :active="request()->routeIs('commission*')">{{ __('Commissions') }}</x-subnav-link>
    </div>
</nav>
