@props(['show' => true, 'type' => 'submit'])

<div x-data="{ value: '{{$show}}' }">
    <button x-show="value" {{$show}} type="{{$type}}" {{ $attributes->except('type')->merge(['class' => 'tx-btn']) }}>
        {{ $slot }}
    </button>
</div>
