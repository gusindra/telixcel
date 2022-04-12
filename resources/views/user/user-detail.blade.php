<x-app-layout>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User') }} : {{$user->name}}
            </h2>
        </div>
    </header>

    <!-- Team Dashboard -->
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-2 bg-white border-b border-gray-200">
                    <div class="mt-2 text-2xl">
                        Overview for
                    </div>
                    <form method="get" action="{{url('/user/'.$user->id)}}">
                        <select name="month" id="">
                            <option {{app("request")->input('month')=='1'?'selected':''}} value="1">January</option>
                            <option {{app("request")->input('month')=='2'?'selected':''}} value="2">February</option>
                            <option {{app("request")->input('month')=='3'?'selected':''}} value="3">March</option>
                            <option {{app("request")->input('month')=='4'?'selected':''}} value="4">April</option>
                            <option {{app("request")->input('month')=='5'?'selected':''}} value="5">May</option>
                            <option {{app("request")->input('month')=='6'?'selected':''}} value="6">June</option>
                            <option {{app("request")->input('month')=='7'?'selected':''}} value="7">July</option>
                            <option {{app("request")->input('month')=='8'?'selected':''}} value="8">August</option>
                            <option {{app("request")->input('month')=='9'?'selected':''}} value="9">September</option>
                            <option {{app("request")->input('month')=='10'?'selected':''}} value="10">October</option>
                            <option {{app("request")->input('month')=='11'?'selected':''}} value="11">November</option>
                            <option {{app("request")->input('month')=='12'?'selected':''}} value="12">December</option>
                        </select>
                        <select name="year" id="">
                            <option {{app("request")->input('year')=='2020'?'selected':''}} value="2020">2020</option>
                            <option {{app("request")->input('year')=='2021'?'selected':''}} value="2021">2021</option>
                            <option {{app("request")->input('year')=='2022'?'selected':''}} value="2022">2022</option>
                        </select>
                        <button type="submit" class="px-2 py-1 bg-gray-800 text-white">Show</button>
                    </form>
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
                                <span>{{$user->clients(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
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
                                <span>{{$user->outbounds(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
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
                                <span>{{$user->inbounds(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
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
                                <span>{{$user->currentTeam->callApi(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
                                <div class="mt-2 text-sm text-gray-500">
                                    <a href="http://telixnet.test/message"> API Call</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-3">
        <div class="max-w-7xl lg:px-8 mx-auto grid lg:grid-cols-6 gap-2">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-sm col-span-3">
                <div class="p-2 bg-white border-b border-gray-200">
                    <div class="mt-2 text-2xl">
                        Team
                    </div>
                </div>

                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                            <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50">
                                <tr>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">Name</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">No Member</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">Created At</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Slug</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @foreach ($user->teams as $team)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="font-medium text-gray-800">{{$team->name}}</div>
                                            </div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{$team->personal_team}}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left font-medium">{{$team->created_at->format('d M Y')}}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-lg font-medium text-green-500 text-center"><a href="{{url('chatting', $team->slug)}}">{{$team->slug}}</a></div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-sm col-span-3">
                <div class="flex justify-between p-2 bg-white border-b border-gray-200">
                    <div class="mt-2 text-2xl">
                        Bill
                    </div>
                    <!-- <div class="text-xs font-medium text-green-500 text-center"><a class="px-2 py-1 bg-gray-800 rounded-md text-white" href="http://telixnet.test/user-billing/create">Create</a></div> -->
                    <div x-data="{ open: false }">
                        <button x-ref="modal1_button"
                                @click="open = true"
                                class="px-2 py-1 bg-gray-800 rounded-md text-white">
                                Create
                        </button>

                        <div role="dialog"
                            aria-labelledby="modal1_label"
                            aria-modal="true"
                            tabindex="0"
                            x-show="open"
                            @click="open = false; $refs.modal1_button.focus()"
                            @click.away="open = false; $refs.modal1_button.focus()"
                            class="fixed top-0 left-0 w-full h-screen flex justify-center items-center">
                            <div class="absolute top-0 left-0 w-full h-screen bg-black opacity-60"
                                aria-hidden="true"
                                x-show="open"></div>
                            <div @click.stop=""
                                x-show="open"
                                class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white w-3/5 h-auto z-10">
                                <div class="p-6 border-b">
                                    <h2 id="modal1_label">Create Bill Invoice</h2>
                                </div>
                                <div class="p-6">
                                    <form method="post" action="{{ route('user.billing.create.invoice') }}">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{$user->id}}" />
                                        <input type="hidden" name="period" value="{{app('request')->input('month').'/'.app('request')->input('year')}}" />
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="status" value="{{ __('Status') }}" />
                                            <select
                                                name="status"
                                                id="status"
                                                class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
                                                wire:model.debunce.800ms="type"
                                                >
                                                <option selected>-- Select Status --</option>
                                                <option value="draft">Draft</option>
                                                <option value="unpaid">Unpaid</option>
                                                <option value="paid">Paid</option>
                                            </select>
                                            <x-jet-input-error for="type" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="code" value="{{ __('Code') }}" />
                                            <x-jet-input id="code" name="code" type="text" class="mt-1 block w-full" autofocus />
                                            <x-jet-input-error for="code" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="description" value="{{ __('Description') }}" />
                                            <textarea name="description" class="mt-1 block w-full">Period : {{app('request')->input('month').'/'.app('request')->input('year')}}&#10;Clients : {{$user->clients(app('request')->input('month'),app('request')->input('year'))->count()}}&#10;In Bounds : {{$user->inbounds(app('request')->input('month'),app('request')->input('year'))->count()}}&#10;Out Bounds : {{$user->outbounds(app('request')->input('month'),app('request')->input('year'))->count()}}&#10;API Requests : {{$user->currentTeam->callApi(app('request')->input('month'),app('request')->input('year'))->count()}}</textarea>
                                            <x-jet-input-error for="description" class="mt-2" />
                                        </div>
                                        <div class="col-span-6 sm:col-span-4 p-3">
                                            <x-jet-label for="amount" value="{{ __('Amount') }}" />
                                            <x-jet-input id="amount" name="amount" type="text" class="mt-1 block w-full" autofocus />
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
                <div class="p-3">
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full">
                            <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50">
                                <tr>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">Code</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">Description</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-left">Amount</div>
                                    </th>
                                    <th class="p-2 whitespace-nowrap">
                                        <div class="font-semibold text-center">Status</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-100">
                                @foreach ($user->billings as $bill)
                                    <tr>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-lg font-medium text-green-500 text-center"><a href="{{route('user.billing.invoice.show', $bill->id)}}">{{$bill->code}}</a></div>
                                            </div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left">{{$bill->description}}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            <div class="text-left font-medium">{{$bill->amount}}</div>
                                        </td>
                                        <td class="p-2 whitespace-nowrap">
                                            {{$bill->status}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Dashboard -->
    <div class="py-3 hidden">
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


</x-app-layout>
