<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if(Auth::user()->role)

    @endif

    @includeWhen(@auth()->user()->listTeams->where('status','!=', NULL)->first()->role=='superadmin', 'dashboard.online', ['status' => 'complete'])

    <!-- First User Member to create Team -->
    @if (Auth::user()->currentTeam && Laravel\Jetstream\Jetstream::hasTeamFeatures())
    <!-- {{Auth::user()->currentTeam}} -->
    @endif


    @if(auth()->user()->currentTeam)
        <!-- Stat -->
        @includeWhen(!auth()->user()->currentTeam && auth()->user()->currentTeam->id!=env('IN_HOUSE_TEAM_ID'), 'dashboard.statistic', ['status' => 'complete'])
        <!-- Asset -->
        @includeWhen(auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin', 'dashboard.asset', ['status' => 'complete'])
        <!-- Task List -->
        @if(auth()->user()->role()->exists() || auth()->user()->super)
            @includeWhen(auth()->user()->currentTeam->id==env('IN_HOUSE_TEAM_ID') || (auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'), 'dashboard.task', ['status' => 'complete'])
        @endif
    @endif

    @if ( Auth::user()->currentTeam && Auth::user()->currentTeam->user_id == Auth::user()->id )
        <!-- Team Dashboard -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                    <x-jet-welcome />
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
