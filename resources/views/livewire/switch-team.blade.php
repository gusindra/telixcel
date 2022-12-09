<div>
    @if($haveTeams>0)
        <!-- Switch Team -->
        <div class="block px-1 py-2 text-xs text-gray-400">
            {{ __('Switch Team') }}
        </div>

        <x-switchable-team :team="auth()->user()->currentTeam ? auth()->user()->currentTeam->name : ''" :selection="auth()->user()->listTeams->where('team_id','!=',1)"></x-switchable-team>

        <!-- <div class="border-t border-gray-100"></div> -->
    @endif
</div>
