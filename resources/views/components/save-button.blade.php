@props(['show' => true, 'type' => 'submit'])

<div x-data="{ value: '{{$show}}' }">
    <button x-show="value" {{$show}} type="{{$type}}" {{ $attributes->except('type')->merge(['class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition']) }}>
        {{ $slot }}
    </button>
</div>
