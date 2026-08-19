<button {{ $attributes->merge(['type' => 'button', 'class' => 'tx-btn tx-btn-ghost']) }}>
    {{ $slot }}
</button>
