<button {{ $attributes->merge(['type' => 'submit', 'class' => 'tx-btn']) }}>
    {{ $slot }}
</button>
