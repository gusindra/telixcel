<div>
    <form wire:submit.prevent="create">
        <div class="md:grid md:grid-cols-3 md:gap-6 mt-8 sm:mt-0">
            <div class="mt-2 md:mt-0 md:col-span-2">
                <div class="px-4 py-5 bg-white dark:bg-slate-600 sm:p-10 shadow sm:rounded-tl-md sm:rounded-tr-md">

                    <div class="col-span-6 sm:col-span-4 mb-8">
                        <div class="col-span-12 sm:col-span-1">
                            <x-jet-label for="nominal" value="{{ __('Nominal') }}" />
                            <div class="flex">
                                <span class="pt-2 px-4 bg-gray-300 dark:bg-slate-700 border border-gray-300  shadow-sm mt-1 ">Rp</span>
                                <x-jet-input id="nominal"
                                            type="text"
                                            class="mt-1 block w-full rounded-r-lg rounded-l-none"
                                            wire:model="nominal"
                                            wire:model.defer="nominal"
                                            wire:model.debunce.800ms="nominal"
                                            x-ref="input"
                                            x-on:change="$dispatch('input', $refs.input.value)"
                                            onfocus="this.select();" />
                            </div>
                            <x-jet-input-error for="nominal" class="mt-2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-8 gap-3">
                        <a wire:click="onClickNominal(100000)" href="#" class="border {{$nominal==100000?'text-green-400 border-green-400':'border-gray-400'}} rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">100rb</a>
                        <a wire:click="onClickNominal(200000)" href="#" class="border border-gray-400 rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">200rb</a>
                        <a wire:click="onClickNominal(500000)" href="#" class="border border-gray-400 rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">500rb</a>
                        <a wire:click="onClickNominal(800000)" href="#" class="border border-gray-400 rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">800rb</a>
                        <a wire:click="onClickNominal(1000000)" href="#" class="border border-gray-400 rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">1jt</a>
                        <a wire:click="onClickNominal(2000000)" href="#" class="border border-gray-400 rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">2jt</a>
                        <a wire:click="onClickNominal(5000000)" href="#" class="border border-gray-400 rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">5jt</a>
                        <a wire:click="onClickNominal(10000000)" href="#" class="border border-gray-400 rounded-lg px-3 py-1 focus:border-green-400 focus:text-green-400 w-20 text-center">10jt</a>
                    </div>
                </div>


            </div>

            <div class="md:col-span-1 px-4 py-5 bg-white dark:bg-slate-600 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                <div class="px-4 sm:px-0 mb-4">
                    <h3 class="font-semibold text-xl text-gray-900 dark:text-slate-300 mb-4">Ringkasan Top up</h3>

                    <p class="px-4 mt-1 text-sm text-gray-600 dark:text-slate-300 flex justify-between">
                        <span>Account</span>
                        <span class="text-right">{{auth()->user()->email}} <br> {{auth()->user()->name}}</span>
                    </p>
                    <p class="px-4 mt-1 text-sm text-gray-600 dark:text-slate-300 flex justify-between">
                        <span>Nominal Top-up</span>
                        <span>Rp {{number_format($nominal)}}</span>
                    </p>
                </div>

                <div class="flex items-center justify-end px-4 py-3 sm:rounded-bl-md sm:rounded-br-md">
                    <x-jet-action-message class="mr-3" on="fail">
                        {{ __('Something is wrong') }}
                    </x-jet-action-message>

                    <button type="submit" class="w-full items-center px-4 py-2 bg-green-800 border border-green-800 dark:border-white/40 dark:bg-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-gray disabled:opacity-25 transition">
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
