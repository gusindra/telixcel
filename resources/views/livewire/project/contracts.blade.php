<div>
    <div wire:poll>
        @if($contracts->count() > 0)
            <!-- Contract -->
            <x-jet-section-border />
            <x-jet-action-section>
                <x-slot name="title">
                    {{ __('Contract') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('List all contract from this project.') }}
                </x-slot>

                <x-slot name="content">

                    <table class="table-auto text-xs sm:text-sm">
                        <thead>
                            <tr>
                                <th class="px-2 py-2">No</th>
                                <th class="px-2 py-2">Title</th>
                                <th class="px-2 py-2">Active Date</th>
                                <th class="px-2 py-2">Expired Date</th>
                                <th class="px-2 py-2">Status</th>
                                <th class="px-2 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contracts as $contract)
                                <tr>
                                    <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium text-center">{{$loop->iteration}} </td>
                                    <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium w-full">{{$contract->title}} </td>
                                    <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">{{is_null($contract->actived_at) ? '-' : $contract->actived_at->format('Y, F d')}}</td>
                                    <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">{{is_null($contract->expired_at) ? '-' : $contract->expired_at->format('Y, F d')}} </td>
                                    <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">{{view('label.label', ['type' => $contract->status])}} </td>
                                    <td class="border border-light-blue-500 px-2 py-2 text-light-blue-600 font-medium">
                                        <a class="flex dark:bg-slate-800 text-xs rounded-md p-2" class="p-2" href="{{route('commercial.edit.show', ['key'=>'contract', 'id'=>$contract->id])}}?source=project&id={{$project->id}}"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                                        </svg>View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-slot>

            </x-jet-action-section>
        @endif
    </div>
</div>
