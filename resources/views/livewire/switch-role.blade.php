<div>
    @if($haveRoles>0)
        <!-- Switch Role -->
        <div class="block px-4 py-2 text-xs text-gray-400">
            {{ __('Switch Role') }}
        </div>

        <x-switchable-role :selection="auth()->user()->currentTeam?auth()->user()->role->where('team_id', auth()->user()->currentTeam->id):[]" :role="auth()->user()->activeRole ? auth()->user()->activeRole->role->name : ''"></x-switchable-role>

        <div class="border-t border-gray-100"></div>
    @endif
</div>
