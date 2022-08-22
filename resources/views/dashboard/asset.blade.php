<div class="py-3">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="py-2 sm:px-2 bg-opacity-10 grid grid-cols-1 md:grid-cols-1 gap-3">
                <div class="flex justify-between">
                    <div class="w-full m-2">
                        <div class="items-center shadow rounded border">
                            <div class="ml-4 text-gray-600 dark:text-gray-300 leading-7 font-semibold text-3xl p-3">
                                <div class="text-sm text-gray-500 dark:text-gray-300">
                                    <a href="#">MacroKiosk</a>
                                </div>
                                <div class="flex justify-between">
                                    <div class="text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold ">
                                        <small>OTP Balance</small>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                            <span class="text-2xl">Rp{{masterSaldo() ? number_format(masterSaldo()) : 0}}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold ">
                                        <small>Non OTP Balance</small>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                            <span class="text-2xl">Rp{{masterSaldo('non') ? number_format(masterSaldo('non')) : 0}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full m-2">
                        <div class="items-center shadow rounded border">
                            <div class="ml-4 text-gray-600 dark:text-gray-300 leading-7 font-semibold text-3xl p-3">
                                <div class="text-sm text-gray-500 dark:text-gray-300">
                                    <a href="#">Order</a>
                                </div>
                                <div class="flex justify-between">
                                    <div class="text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold ">
                                        <small>Draft</small>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                            <span class="text-2xl">{{masterOrder("draft") ? masterOrder("draft") : 0}}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold ">
                                        <small>Unpaid</small>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                            <span class="text-2xl">{{masterOrder('unpaid') ? masterOrder('unpaid') : 0}}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 text-lg text-gray-600 dark:text-gray-300 leading-7 font-semibold ">
                                        <small>Paid</small>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-300">
                                            <span class="text-2xl">{{masterOrder('paid') ? masterOrder('paid') : 0}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
