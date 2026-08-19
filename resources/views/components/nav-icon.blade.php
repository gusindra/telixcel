@props(['name' => 'dot'])

@php
$symbols = [
    'dashboard' => 'space_dashboard',
    'users'     => 'group',
    'billing'   => 'receipt_long',
    'spark'     => 'auto_awesome',
    'chat'      => 'chat',
    'ticket'    => 'confirmation_number',
    'briefcase' => 'work',
    'folder'    => 'folder',
    'file'      => 'description',
    'cart'      => 'shopping_cart',
    'settings'  => 'settings',
    'dot'       => 'circle',
];
$symbol = $symbols[$name] ?? $symbols['dot'];
@endphp

<span class="material-symbols-outlined" aria-hidden="true">{{ $symbol }}</span>
