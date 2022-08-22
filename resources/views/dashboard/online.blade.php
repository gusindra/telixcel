<div class="py-3">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-transparent dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="py-2 sm:px-2 bg-opacity-10 grid grid-cols-1 md:grid-cols-1 gap-3">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1">No</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">User</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-xs leading-4 font-medium text-center text-gray-500 uppercase tracking-wider w-1">Status</th>
                            <th class="px-6 py-3 bg-gray-50 dark:bg-slate-800 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-2">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-gray-200">
                        @foreach (list_online() as $item)
                            <tr class=" ">
                                <td class="px-6 py-2 text-sm whitespace-no-wrap align-top"> {{$loop->iteration}} </td>
                                <td class="px-6 py-2 text-sm whitespace-no-wrap align-top"> {{$item->user->name}} </td>
                                <td class="px-2 py-2 text-sm whitespace-no-wrap text-center align-top"> {{$item->status}} </td>
                                <td class="px-2 py-2 text-sm whitespace-no-wrap flex"> {{$item->updated_at}} </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
