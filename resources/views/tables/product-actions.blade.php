<div class="flex space-x-1 justify-around">
    <a href="{{ $url }}" target="_blank" class="p-1 text-xs text-blue-600 border bg-gray-300 dark:bg-slate-800 dark:text-slate-300 hover:bg-blue-500 hover:text-white rounded">
        Edit
    </a>

    <x-modal :value="$id">
        <x-slot name="trigger">
            <button class="p-1 text-blue-600 dark:text-slate-300 border bg-gray-300 dark:bg-slate-800 hover:bg-blue-600 hover:text-white rounded text-xs">
                Syn
            </button>
        </x-slot>
        <h4 class="text-base text-purple-700 mb-2">Synchronize {{ $name }}</h4>
        <a href="{{ $url }}" target="_blank" class="p-1 m-1 text-xs bg-blue-100 text-blue-600 hover:bg-blue-500 hover:text-white rounded">
            Update from Ginee
        </a>
        <a href="{{ $url }}" target="_blank" class="p-1 m-1 text-xs bg-blue-100 text-blue-600 hover:bg-blue-500 hover:text-white rounded">
            Update to Ginee
        </a>
    </x-modal>

    <!-- @include('datatables::delete', ['value' => $id]) -->
</div>
