<div>
    @props(['team','selection', 'component' => 'jet-dropdown-link'])
    <form>
        @method('PUT')
        @csrf
        @foreach ($selection as $select)
            <x-dynamic-component :component="$component" href="#" wire:click="updateTeam('{{$select->team_id}}')">
                <div class="flex items-center">
                    @if($team === $select->team->name)
                    <svg class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                    <div class="truncate text-sm">{{$select->team->name}}</div>
                </div>
            </x-dynamic-component>
        @endforeach
    </form>
</div>
