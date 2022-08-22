<div>
    @if($list_orders && count($list_orders)>0)
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="md:grid md:grid-cols-5 md:gap-6 mt-8 sm:mt-0">
            <div class="md:col-span-1"></div>
            <div class="md:col-span-3 px-4 py-5 bg-white dark:bg-slate-600 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                <div class="px-4 sm:px-0 mb-4">
                    <div class="flex justify-between my-1">
                        <span class="font-semibold text-xl text-gray-900 mb-4">Menunggu Pembayaran</span>
                    </div>

                    @foreach ($list_orders as $order)
                        <div class="flex justify-between my-4 shadow dark:bg-slate-800">
                            <img class="w-20 h-6" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/2022/v2_logo-bca.png" />
                            <p class="text-gray-600 ">
                                <span>Top-up</span><br>
                                <span class="text-xs font-semibold">[ {{$order->created_at->format('d F Y')}} ]</span><br>
                                <span class="text-xs font-semibold">Metode Pembayaran<br>Transfer Manual</span>
                            </p>
                            <div class="copy mx-4">
                                <p class="text-right text-sm">Total pembayaran:<br> Rp{{number_format($order->total)}}</p>
                                <div class="flex justify-between gap-2 items-center py-3 sm:rounded-bl-md sm:rounded-br-md">
                                    <a href="{{route('invoice.topup', $order->id)}}" class="w-auto text-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-gray disabled:opacity-25 transition">
                                        Lihat Detail
                                    </a>
                                    @if($order->status == 'unpaid')
                                        <a wire:click="actionShowModal({{$order->id}})"  class="w-auto cursor-pointer text-center px-4 py-2 bg-gray-100 border border-gray-600 rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-200 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition">
                                            Batalkan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
                <x-jet-action-message class="mr-3" on="cancel">
                    {{ __('Cancel transaction') }}
                </x-jet-action-message>
            </div>
            <div class="md:col-span-1"></div>
        </div>
    </div>
    <!-- Modal Detail -->
    <x-jet-dialog-modal wire:model="modalDetail">
        <x-slot name="title">
            <div class="text-center font-bold text-2xl">{{ __('Yakin ingin batalkan transaksi?') }}</div>
        </x-slot>

        <x-slot name="content">
            <div class="p-4">
                <div class="flex justify-between py-2">
                    <span>{{ __('Konfirmasi Pembatalan Transaksi Anda') }}</span>
                </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalDetail')" wire:loading.attr="disabled">
                {{ __('Back') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2 " wire:click="cancelTransaction" wire:loading.attr="disabled">
                {{ __('Yes') }}
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
    @endif
</div>
