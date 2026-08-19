@props(['disabled' => false])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full border border-gray-300 p-2 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:cursor-not-allowed disabled:opacity-100 dark:bg-slate-800 dark:text-slate-300']) !!}>
    {{ $slot }}
</select>
