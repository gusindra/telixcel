<div class="p-2 sm:px-20 bg-white border-b border-gray-200">
    <div>
        <!-- <x-jet-application-logo class="block h-12 w-auto" /> -->
    </div>

    <div class="mt-2 text-2xl">
        Overview for {{ Auth::user()->currentTeam->name }}
    </div>

    <!-- <div class="mt-6 text-gray-500">
        Telixcel provides a beautiful, robust starting point for your next application. Telixcel is designed
        to help you build your client notification via Whatsapp using a environment that is simple, powerful, and enjoyable.  We hope you love it.
    </div> -->
</div>

<div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-3">
    <x-dashboard-stat :client="Auth::user()->currentTeam->client->count()" :inbound="Auth::user()->currentTeam->requestIn->count()" :outbound="Auth::user()->currentTeam->requestOut->count()"></x-dashboard-stat>
</div>
