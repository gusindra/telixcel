<nav class="tx-subnav" aria-label="Commercial">
    <div class="tx-subnav-track">
        <x-subnav-link href="{{ route('commercial.show', ['item']) }}" :active="($key ?? '') == 'item'">{{ __('Product Master Data') }}</x-subnav-link>
        <x-subnav-link href="{{ route('commercial.show', ['quotation']) }}" :active="($key ?? '') == 'quotation'">{{ __('Quotation') }}</x-subnav-link>
        <x-subnav-link href="{{ route('commercial.show', ['contract']) }}" :active="($key ?? '') == 'contract'">{{ __('Contract') }}</x-subnav-link>
    </div>
</nav>
