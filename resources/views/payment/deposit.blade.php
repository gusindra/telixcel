<x-app-layout>
    <header class="bg-white dark:bg-slate-900 shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <big class="font-semibold text-xl text-gray-800 dark:text-slate-300 leading-tight flex justify-between">
                <a class="hover:text-gray-400" href="{{route('profile.show')}}"><span class="capitalize">{{auth()->user()->name}}</span></a>
                <div>
                    <a href="#">{{ __('Balance') }} : <span class="capitalize">Rp {{number_format(balance(auth()->user()))}}</span></a>
                    <a  href="{{route('payment.topup')}}" class="ml-3 border text-xs bg-green-400 rounded text-white border-gray-200 align-middle p-1">Top-up</a>
                </div>
            </big>

            @if(balance(auth()->user())!=0)
            <p class="text-right">
                <small>
                    {{ __('Estimation') }} :
                    @foreach(estimationSaldo() as $product)
                        <span class="capitalize text-xs">{{$product->name}} ({{number_format(balance(auth()->user())/$product->unit_price)}} SMS)</span>
                    @endforeach
                </small>
            </p>
            @endif
        </div>
    </header>

    <!-- Team Dashboard -->
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-2 border-b border-gray-200">
                    <div class="mt-2 text-2xl">
                        History Balance Saldo
                    </div>
                </div>

                <div class="p-3">
                    <livewire:table.balance user="{{auth()->user()->id}}" exportable />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
