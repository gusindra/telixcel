<x-app-layout>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Invoice {{$billing->code}}
            </h2>
        </div>
    </header>

    <!-- Team Dashboard -->
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-2 bg-white border-b border-gray-200 flex justify-between">
                    <div class="mt-2 text-2xl">
                        Periode {{ $billing->period }}
                    </div>
                    <div x-data="{ open: false }">
                        <button x-ref="modal1_button"
                                @click="open = true"
                                class="px-2 py-1 bg-gray-800 rounded-md text-white">
                                Update
                        </button>

                        <div role="dialog"
                            aria-labelledby="modal1_label"
                            aria-modal="true"
                            tabindex="0"
                            x-show="open"
                            @click="open = false; $refs.modal1_button.focus()"
                            @click.away="open = false; $refs.modal1_button.focus()"
                            class="fixed top-0 left-0 w-full h-screen flex justify-center items-center z-50">
                            <div class="absolute top-0 left-0 w-full h-screen bg-black opacity-60"
                                aria-hidden="true"
                                x-show="open"></div>
                            <div @click.stop=""
                                x-show="open"
                                class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white w-3/5 h-auto z-10">
                                <div class="p-6 border-b">
                                    <h2 id="modal1_label">Update Invoice</h2>
                                </div>
                                <div class="p-6">
                                    <form method="post" action="{{ route('user.billing.update.invoice', [$billing->id]) }}">
                                        <input type="hidden" name="_method" value="PUT">
                                        @csrf
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="status" value="{{ __('Status') }}" />
                                            <select
                                                name="status"
                                                id="status"
                                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                                wire:model.debunce.800ms="type"
                                                >
                                                <option selected>-- Select Status --</option>
                                                <option {{$billing->status == 'draft' ? ' selected':''}} value="draft">Draft</option>
                                                <option {{$billing->status == 'unpaid' ? ' selected':''}} value="unpaid">Unpaid</option>
                                                <option {{$billing->status == 'paid' ? ' selected':''}} value="paid">Paid</option>
                                            </select>
                                            <x-jet-input-error for="type" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="code" value="{{ __('Code') }}" />
                                            <x-jet-input id="code" name="code" type="text" class="mt-1 block w-full" autofocus value="{{ $billing->code }}"/>
                                            <x-jet-input-error for="code" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="period" value="{{ __('Period') }}" />
                                            <x-jet-input id="period" name="period" type="text" class="mt-1 block w-full" autofocus value="{{ $billing->period }}"/>
                                            <x-jet-input-error for="period" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="description" value="{{ __('Description') }}" />
                                            <textarea name="description" class="mt-1 block w-full">{{ $billing->description }}</textarea>
                                            <x-jet-input-error for="description" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="amount" value="{{ __('Amount') }}" />
                                            <x-jet-input id="amount" name="amount" type="text" class="mt-1 block w-full" autofocus value="{{ $billing->amount }}" />
                                            <x-jet-input-error for="amount" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3 text-right">
                                            <x-jet-button class="ml-4">
                                                {{ __('Save') }}
                                            </x-jet-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-4">
                    <div class="p-6">
                        <div class="flex items-center">
                            <a href="http://telixnet.test/client">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-2 font-semibold text-3xl">
                                <span>{{$user->clients(explode('/', $billing->period)[0],explode('/', $billing->period)[1])->count()}}</span>
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/client">Client</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center">
                            <a href="http://telixnet.test/message">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold text-3xl">
                                <span>{{$user->outbounds(explode('/', $billing->period)[0],explode('/', $billing->period)[1])->count()}}</span>
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/message">Out Bound</a>
                                </div>
                            </div>
                        </div>

                        <div class="ml-12">
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center">
                            <a href="http://telixnet.test/message">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold text-3xl">
                                <span>{{$user->inbounds(explode('/', $billing->period)[0],explode('/', $billing->period)[1])->count()}}</span>
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/message"> In Bound</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center">
                            <a href="http://telixnet.test/message">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                </svg>
                            </a>
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold text-3xl">
                                <span>{{$user->currentTeam->callApi(explode('/', $billing->period)[0],explode('/', $billing->period)[1])->count()}}</span>
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/message"> API Call</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25 grid grid-cols-3">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-2 font-semibold text-3xl">
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/message"> Description</a>
                                </div>
                                <span>{{$billing->description}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-2 font-semibold text-3xl">
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/message"> Total</a>
                                </div>
                                <span>IDR {{$billing->amount}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-2 font-semibold text-3xl">
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/message"> Status</a>
                                </div>
                                <span>{{$billing->status}}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="py-3">
        <div class="max-w-7xl lg:px-8 mx-auto">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-sm col-span-3">
                <div class="p-2 bg-white border-b border-gray-200">
                    <div class="mt-2 text-2xl">
                        Client
                    </div>
                </div>

                <div class="p-3">
                    <div class="overflow-x-auto">
                        <livewire:user-billing-table userId="{{$billing->user_id}}" month="{{explode('/', $billing->period)[0]}}" year="{{explode('/', $billing->period)[1]}}" searchable="id" exportable />
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-sm col-span-3">
                <div class="flex justify-between p-2 bg-white border-b border-gray-200">
                    <div class="mt-2 text-2xl">
                        Request
                    </div>
                </div>
                <div class="p-3">
                    <div class="overflow-x-auto">
                        <livewire:request-billing-table userId="{{$billing->user_id}}" month="{{explode('/', $billing->period)[0]}}" year="{{explode('/', $billing->period)[1]}}" searchable="id" exportable />
                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
