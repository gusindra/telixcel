<div class="p-2 sm:px-20 bg-white dark:bg-slate-600 border-b border-gray-200">
    <div>
        <!-- <x-jet-application-logo class="block h-12 w-auto" /> -->
    </div>

    <div class="mt-2 text-2xl">
        Overview for {{ Auth::user()->currentTeam->name }}
    </div>
</div>

<div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-4">
    <x-dashboard-stat :api="Auth::user()->currentTeam->callApi->count()" :client="Auth::user()->currentTeam->client->count()" :inbound="Auth::user()->currentTeam->requestIn->count()" :outbound="Auth::user()->currentTeam->requestOut->count()"></x-dashboard-stat>
</div>
