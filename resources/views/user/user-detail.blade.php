<x-app-layout>
    <header class="bg-white dark:bg-slate-900 dark:border-slate-600 border-b shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <big class="font-semibold text-xl text-gray-800 dark:text-slate-300 leading-tight">
                {{ __('User Name') }} : <span class="capitalize">{{$user->name}}</span>
            </big>
        </div>
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 justify-end flex">
            <div>
                <div class="items-center justify-end px-2 text-right">
                    <x-jet-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md mb-2">
                                <button type="button" class="inline-flex items-center px-3 py-2 border text-xs leading-4 font-medium rounded-md text-gray-500  bg-gray-200 hover:bg-gray-300 hover:text-gray-700 focus:outline-none focus:bg-gray-400 active:bg-gray-400 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-60">
                                <div>
                                    <form>
                                        <a class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-slate-400 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition" target="_blank" href="{{route('user.show.profile', ['user'=>$user->id])}}">
                                            <div class="flex items-center justify-between">
                                                <div class="truncate">Profile</div>
                                            </div>
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-jet-dropdown>
                </div>
            </div>
        </div>
    </header>

    @if($user->id != 0)
        {{-- Dashboard-style overview, scoped to this user's owned / assigned tasks --}}
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-2">
                <h3 class="text-lg font-medium text-gray-900 dark:text-slate-200">{{ __('Tasks') }} — <span class="capitalize">{{ $user->name }}</span></h3>
                <p class="text-sm text-gray-500 dark:text-slate-300">{{ __('Tasks owned by or assigned to this user.') }}</p>
            </div>
            @livewire('dashboard.dashboard-overview', ['forUserId' => $user->id], key('user-dashboard-'.$user->id))
        </div>

        {{-- Team projects overview --}}
        @include('dashboard.project', ['ownerId' => $user->id])

        {{-- Teams this user belongs to --}}
        <div class="py-3 mb-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200 dark:border-slate-500 text-lg font-medium text-gray-900 dark:text-slate-200">{{ __('Team') }}</div>
                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full">
                                <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-700">
                                    <tr>
                                        <th class="p-2 text-left">Name</th>
                                        <th class="p-2 text-left">No Member</th>
                                        <th class="p-2 text-left">Created At</th>
                                        <th class="p-2 text-center">Slug</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-100 dark:divide-slate-500">
                                    @forelse ($user->teams as $team)
                                        <tr>
                                            <td class="p-2 whitespace-nowrap font-medium text-gray-800 dark:text-slate-200">{{ $team->name }}</td>
                                            <td class="p-2 whitespace-nowrap">{{ $team->personal_team }}</td>
                                            <td class="p-2 whitespace-nowrap">{{ $team->created_at->format('d M Y') }}</td>
                                            <td class="p-2 whitespace-nowrap text-center"><a class="text-green-500 font-medium" href="{{ url('chatting', $team->slug) }}">{{ $team->slug }}</a></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="p-4 text-center text-gray-400">{{ __('No team.') }}</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
