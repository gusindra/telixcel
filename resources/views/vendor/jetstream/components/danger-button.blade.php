<button {{ $attributes->merge(['type' => 'button', 'class' => 'tx-btn tx-btn-danger']) }}>
    {{ $slot }}
</button>
