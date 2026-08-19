<x-sidebar-link href="{{ route('project') }}" :active="$isProject" :label="__('Project')">
    <x-nav-icon name="folder" />
    <span class="tx-nav-label">{{ __('Project') }}</span>
</x-sidebar-link>
<x-sidebar-link href="{{ route('commercial') }}" :active="$isCommercial" :label="__('Commercial')">
    <x-nav-icon name="file" />
    <span class="tx-nav-label">{{ __('Commercial') }}</span>
</x-sidebar-link>
<x-sidebar-link href="{{ route('order') }}" :active="$isOrder" :label="__('Order')">
    <x-nav-icon name="cart" />
    <span class="tx-nav-label">{{ __('Order') }}</span>
</x-sidebar-link>
