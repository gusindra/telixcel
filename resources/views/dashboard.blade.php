<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Personal Dashboard -->
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="py-6 sm:px-10 bg-opacity-10 grid grid-cols-1 md:grid-cols-4 gap-5">
                    <div class="col-span-2 row-span-3">
                        <div class="h-full">
                            <livewire:personal-chart/>
                        </div>
                    </div>

                    <div class=" col-span-1">
                        <div class="flex shadow rounded p-2 border bg-white">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold ">
                                <small>No. of conversation</small>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="text-2xl">{{auth()->user()->currentTeam->requestAll->where('from', auth()->user()->id)->count()}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex shadow rounded p-2 mt-5 border bg-white">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                <small>Avg duration of conversation</small>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class=" col-span-1">
                        <div class="flex shadow rounded p-2  border bg-white">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                <small>Time of response</small>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex shadow rounded p-2 mt-5 border bg-white">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                <small>Satisfactory Rate</small>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=" col-span-1">
                        <div class="flex shadow rounded p-2  border bg-white">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                <small>SMS Bulk request</small>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" col-span-1">
                        <div class="flex shadow rounded p-2 border bg-white">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                <small>SMS Sent</small>
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2">

                        <div class="flex items-center shadow rounded border bg-white">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold text-3xl p-3">
                                <div class="text-sm text-gray-500">
                                    <a href="#">Status overview</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if ( Auth::user()->currentTeam && Auth::user()->currentTeam->user_id == Auth::user()->id )
        <!-- Team Dashboard -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <x-jet-welcome />
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
