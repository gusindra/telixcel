<x-app-layout>
    <header class="bg-white dark:bg-slate-900 dark:border-slate-600 border-b shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <big class="font-semibold text-xl text-gray-800 dark:text-slate-300 leading-tight">
                {{ __('User Name') }} : <span class="capitalize">{{$user->name}}</span>
                -
                <a class="hover:text-gray-400 dark:text-slate-300" href="{{route('user.show.balance', $user->id)}}">{{ __('Balance') }} : <span class="capitalize">Rp {{number_format(balance($user))}}</span></a>
            </big>
            <br>
            @if(balance($user)!=0)
            <small>
                {{ __('Estimation') }} :
                @foreach(estimationSaldo() as $product)
                    <span class="capitalize">{{$product->name}} ({{number_format(balance($user)/$product->unit_price)}} SMS)</span>
                @endforeach
            </small>
            @endif
        </div>
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 justify-between flex">
            @livewire('saldo.topup', ['user' => $user])

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

    @if($user->id!=0)
        <!-- Team Dashboard -->
        <div class="py-3">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-2 border-b border-gray-200">
                        <div class="mt-2 text-2xl">
                            Overview for
                        </div>
                        <form method="get" action="{{url('/user/'.$user->id)}}">
                            <select class="dark:bg-slate-800 dark:text-slate-300" name="month" id="month">
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
                            <select class="dark:bg-slate-800 dark:text-slate-300" name="year" id="year">
                                @for ($i=date('Y'); $i > date('Y')-5 ; $i--)
                                    <option {{app("request")->input('year')==$i?'selected':''}} value="{{$i}}">{{$i}}</option>
                                @endfor
                            </select>
                            <button type="submit" class="px-2 py-1 bg-gray-800 text-white">Show</button>
                        </form>
                    </div>

                    <div class="p-6 sm:px-20 bg-gray-200 dark:bg-slate-600 bg-opacity-25 grid grid-cols-1 md:grid-cols-4">
                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/client">
                                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 dark:text-slate-300 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-2 font-semibold text-3xl">
                                    <span>{{$user->clients(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/client">Client</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/message">
                                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 dark:text-slate-300 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-7 font-semibold text-3xl">
                                    <span>{{$user->outbounds(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/message">Out Bound</a>
                                    </div>
                                </div>
                            </div>

                            <div class="ml-12">
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/message">
                                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-16 dark:text-slate-300 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-7 font-semibold text-3xl">
                                    <span>{{$user->inbounds(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/message"> In Bound</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/message">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 text-gray-400 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-7 font-semibold text-3xl">
                                    <span>{{$user->currentTeam->callApi(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/message"> API Call</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/report/sms">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 text-gray-400 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-7 font-semibold text-3xl">
                                    <span>{{$user->sentsms(app('request')->input('month'),app('request')->input('year'),'total')->count()}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/report/sms"> Total SMS</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/report/sms">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 text-gray-400 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-7 font-semibold text-3xl">
                                    <span>{{$user->sentsms(app('request')->input('month'),app('request')->input('year'))->count()}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/report/sms"> SMS DELIVERED</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/report/sms">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 text-gray-400 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-7 font-semibold text-3xl">
                                    <span>{{$user->sentsms(app('request')->input('month'),app('request')->input('year'), 'UNDELIVERED')->count()}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/report/sms"> SMS UNDELIVERED</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <a href="http://telixcel.com/report/sms">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 text-gray-400 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </a>
                                <div class="ml-4 text-gray-600 dark:text-slate-300 leading-7 font-semibold text-2xl">
                                    <span>Rp {{number_format($user->sentsms(app('request')->input('month'),app('request')->input('year'))->sum('price'))}}</span>
                                    <div class="mt-2 text-sm text-gray-500 dark:text-slate-300">
                                        <a href="http://telixcel.com/report/sms"> SMS COST</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-3 mb-4">
            <div class="max-w-7xl lg:px-8 mx-auto grid lg:grid-cols-6 gap-2">
                <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-sm col-span-3">
                    <div class="p-2 border-b border-gray-200">
                        <div class="mt-2 text-2xl">
                            Team
                        </div>
                    </div>

                    <div class="p-3">
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full">
                                <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-700">
                                    <tr>
                                        <th class="p-2 whitespace-nowrap">
                                            <div class="font-semibold text-left">Name</div>
                                        </th>
                                        <th class="p-2 whitespace-nowrap">
                                            <div class="font-semibold text-left">No Member</div>
                                        </th>
                                        <th class="p-2 whitespace-nowrap">
                                            <div class="font-semibold text-left">Balance</div>
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
                                                <div class="text-left">Rp {{number_format(balance($user, $team->id))}}</div>
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
                <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-sm col-span-3">
                    <div class="flex justify-between p-2 border-b border-gray-200">
                        <div class="mt-2 text-2xl">
                            Bill
                        </div>
                        <!-- <div class="text-xs font-medium text-green-500 text-center"><a class="px-2 py-1 bg-gray-800 rounded-md text-white" href="http://telixcel.com/user-billing/create">Create</a></div> -->
                        <div x-data="{ open: false }">
                            <!-- <button x-ref="modal1_button"
                                    @click="open = true"
                                    class="px-2 py-1 bg-gray-800 rounded-md text-white">
                                    Create
                            </button> -->

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
                                    class="flex flex-col rounded-lg shadow-lg overflow-hidden  w-3/5 h-auto z-10">
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
                                <thead class="text-xs font-semibold uppercase text-gray-400 bg-gray-50 dark:bg-slate-700">
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

    @endif

</x-app-layout>
