@props(['disabled' => false])

<textarea contenteditable {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm dark:bg-slate-900 dark:text-slate-300']) !!}></textarea>
