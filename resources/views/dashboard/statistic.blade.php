<div class="py-3">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="py-2 sm:px-2 bg-opacity-10 grid grid-cols-1 md:grid-cols-1 gap-3">
                <div class="col-span-2 row-span-3 m-2">
                    <div class="h-full">
                        <livewire:personal-chart />
                    </div>
                </div>
                <div class="flex justify-content">
                    <div class="m-2 w-full">
                        <div class="flex shadow rounded p-2 border">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold ">
                                <small>No. of conversation</small>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                    <span class="text-2xl">{{auth()->user()->currentTeam->requestAll->where('from', auth()->user()->id)->count()}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex shadow rounded p-2 mt-2 border">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold">
                                <small>Avg duration of conversation</small>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="m-2 w-full">
                        <div class="flex shadow rounded p-2 border">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold">
                                <small>Time of response</small>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex shadow rounded p-2 mt-2 border">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold">
                                <small>Satisfactory Rate</small>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m-2 w-full">
                        <div class="flex shadow rounded p-2  border">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold">
                                <small>SMS Bulk request</small>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="m-2 w-full">
                        <div class="flex shadow rounded p-2 border">
                            <a href="#">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-6 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold">
                                <small>SMS Sent</small>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                    <span class="text-2xl">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
