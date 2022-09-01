<x-app-layout>
    <header class="bg-white dark:bg-slate-600 shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <big class="font-semibold text-xl text-gray-800 dark:text-slate-300 leading-tight">
                <a class="hover:text-gray-400" href="{{route('user.show', $user->id)}}">{{ __('User Name') }} : <span class="capitalize">{{$user->name}}</span></a>
                -
                <a href="#">{{ __('Balance') }} : <span class="capitalize">Rp {{number_format(balance($user))}}</span></a>
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
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            @livewire('saldo.topup', ['user' => $user])
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
                    <livewire:table.balance user="{{$user->id}}" exportable />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
