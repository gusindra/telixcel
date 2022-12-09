<div x-data="{ value: '{{$show}}' }">
    <button x-show="value" {{$show}} {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-slate-700 dark:text-slate-300 uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none dark:hover:text-slate-800 hover:text-slate-800 focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition']) }}>
        {{ $slot }}
    </button>
</div>
