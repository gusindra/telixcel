<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden sm:rounded-lg"></div>
        <div class="py-3">
            <div class="md:grid md:grid-cols-4 md:gap-6 mt-8 sm:mt-0">
                <div class="md:col-span-1"></div>
                <div class="md:col-span-2 px-4 py-5 bg-white dark:bg-slate-600 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                    <div class="px-4 sm:px-0 mb-4">
                        <div class="flex justify-between my-1">
                            <span class="font-semibold text-xl text-gray-900 dark:text-slate-300 mb-4">Transfer Bank</span>
                            <img class="w-20 h-6 dark:bg-white" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/imgs/2022/v2_logo-bca.png">
                            <span>Invoice No {{$order->no}} <span class="text-xs uppercase {{$order->status=='paid'?'text-green-600':'text-red-600'}}">{{$order->status=='paid'?'[PAID]':'['.$order->status.']'}}</span></span>
                        </div>

                        <div class="flex justify-between my-4" x-data="{alert:false}">
                            <p class="mx-4 mt-1 text-gray-600 dark:text-slate-300">
                                <span>No Rekening</span><br>
                                <span class="text-xl font-semibold"> 505 5564 644</span><br>
                                <input id="rekening" class="text-xl font-semibold hidden" value="5055564644" />
                                <span class="text-xs font-semibold">PT TELIXCEL CENTRIX INDONESIA <br>(KCP EPICENTRUM WALK)</span>
                            </p>
                            <p class="copy mx-4 mt-6" data-for="#rekening">
                                <button class="text-lg font-semibold text-green-600" @click="alert = true">Salin</button>
                                <span class="text-xs" x-show="alert" @click="alert = false">Copied!</span>
                            </p>
                        </div>
                        <div class="flex justify-between" x-data="{ input: 'Foo!' }">
                            <p class="mx-4 mt-1 text-gray-600 dark:text-slate-300">
                                <span>Total Pembayaran</span><br>
                                <span class="text-xl font-semibold">Rp {{number_format($order->total)}}</span>
                            </p>
                            <p class="mx-4 mt-1">
                                <a wire:click="actionShowModal('detail')" href="#" x-clipboard="input" class="text-lg font-semibold text-green-600">Detail</a>
                            </p>
                        </div>
                    </div>
                    @if($order->status=='unpaid')
                    <x-jet-action-message class="mr-3" on="saved">
                        {{ __('Checking payment..') }}
                    </x-jet-action-message>
                    <x-jet-action-message class="mr-3" on="already">
                        {{ __('Your request has been processed..') }}
                    </x-jet-action-message>
                    <div class="flex justify-between gap-2 items-center px-4 py-3 sm:rounded-bl-md sm:rounded-br-md">
                        <button {{$check_status==0?'disabled':''}} wire:click="checkPayment" class="w-full items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-gray disabled:opacity-25 transition">
                            Cek Pembayaran
                        </button>
                        <a wire:click="actionShowModal('upload')"  type="submit" class="w-full cursor-pointer text-center px-4 py-2 bg-green-100 border border-green-600 rounded-md font-semibold text-xs text-green-800 uppercase tracking-widest hover:bg-green-200 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-gray disabled:opacity-25 transition">
                            Upload Bukti Pembayaran
                        </a>
                    </div>
                    @endif
                    <script>
                        // Prepare action buttons
                        const buttonContainers = document.querySelectorAll('.copy');

                        for (const buttonContainer of buttonContainers) {
                            const buttons = buttonContainer.querySelectorAll('button');
                            const pasteTarget = buttonContainer.getAttribute('data-for');

                            for (const button of buttons) {
                                const elementName = button.getAttribute('data-el');
                                button.addEventListener('click', () => insertText(pasteTarget))
                            }
                        }

                        // Inserts text at cursor, or replaces selected text
                        function insertText(selector) {
                            const text = document.querySelector(selector);
                            try {
                                // Security exception may be thrown by some browsers
                                const textArea = document.createElement('textarea');
                                textArea.value = text.value;
                                document.body.appendChild(textArea);
                                textArea.select();
                                document.execCommand('Copy');
                                textArea.remove();
                            } catch (ex) {
                                console.warn("Copy to clipboard failed.", ex);
                                return false;
                            } finally {
                            }
                        }
                    </script>
                </div>
                <div class="md:col-span-1"></div>
                <div class="md:col-span-1"></div>
                <div class="md:col-span-2 px-4 py-5 bg-white dark:bg-slate-600 sm:p-6 shadow sm:rounded-tl-md sm:rounded-tr-md">
                    <div class="px-4 sm:px-0 mb-4">
                        @if($order->status=='unpaid')
                            <h3 class="font-semibold text-xl text-gray-900 dark:text-slate-300 mb-2">Cara pembayaran</h3>
                            <p class="mt-1 text-sm text-gray-600 flex justify-between mb-3">
                                <ol>
                                    <li class="text-sm">1. Pilih Menu Transfer.</li>
                                    <li class="text-sm">2. Masukan Nama Tujuan Bank BCA, No Rekening dan Nominal sesuai rincian di atas.</li>
                                    <li class="text-sm">3. Pastikan kembali Nama dan Rekening Penerima sudah Sesuai dan pembayaran Anda sudah BERHASIL</li>
                                    <li class="text-sm">4. Unggah bukti untuk mempercepat proses verifikasi</li>
                                </ol>
                            </p>
                            <table class="table-striped w-full hidden">
                                <tbody class="border border-gray-300">
                                    <tr>
                                        <td class="p-2 text-sm">Bank Name</td>
                                        <td class="p-2 text-sm">BCA - Bank Central Asia</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 text-sm">Bank Address</td>
                                        <td class="p-2 text-sm">KCP EPICENTRUM WALK - JAKARTA SELATAN</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 text-sm">Account Name</td>
                                        <td class="p-2 text-sm">PT TELIXCEL CENTRIX INDONESIA</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2 text-sm">Account No</td>
                                        <td class="p-2 text-sm">505-5564-644</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                        <p class="mt-1 text-xs text-gray-600 dark:text-slate-300 flex justify-between pt-6 pb-2">
                            Catatan
                            <li class="text-xs ">Simpan bukti pembayaran yang sewaktu-waktu diperlukan jika terjadi kendala transaksi</li>
                            <li class="text-xs ">Silakan kutip No Invoice saat pembayaran dilakukan</li>
                            <li class="text-xs ">Pertanyaan terkait pembayaran dapat dikirimkan ke e-mail: <a class="text-blue-600 dark:text-blue-400" href="mailto:support@telixcel.com">support@telixcel.com</a></li>
                        </p>
                    </div>
                </div>
                <div class="md:col-span-1"></div>
            </div>
        </div>

    </div>

    <!-- Modal Detail -->
    <x-jet-dialog-modal wire:model="modalDetail">
        <x-slot name="title">
            <div class="text-center font-bold text-2xl">{{ __('Detail Pembayaran') }}</div>
            <div class="text-center p-2 {{$order->status=='paid'?'text-green-600':'text-red-600'}}">[ {{$order->status}} ]</div>
        </x-slot>

        <x-slot name="content">
            <div class="p-4">
                <div class="flex justify-between py-2">
                    <span>{{ __('Total Harga') }}</span>
                </div>
                <hr>
                <div class="py-2">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between capitalize">
                            <span>{{ $item->name }} <small>{{ $item->note }}</small></span>
                            <span>Rp{{ number_format($item->price) }}</span>
                        </div>
                    @endforeach
                </div>
                <hr>
                <div class="flex justify-between py-2">
                    <span class="font-bold text-xl">{{ __('Total Bayar') }}</span>
                    <span class="font-bold text-xl">Rp{{number_format($order->total)}}</span>
                </div>
                <hr>
                <div class="flex justify-between pt-2">
                    <span>{{ __('Dibayar dengan') }}</span>
                </div>
                <div class="flex justify-between pb-2">
                    <span>{{ __('Transfer Bank') }}</span>
                    <span>Rp{{number_format($order->total)}}</span>
                </div>
                <hr>
                <div class="flex justify-between py-2">
                    <span class="font-bold text-xl">{{ __('Produk yang dibayar') }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="font-bold">{{ __('Topup') }}</span>
                    <span class="text-right">
                        {{number_format($order->items[0]->price)}}<br>
                        @foreach(estimationSaldo() as $product)
                            <span class="text-xs capitalize">Estimation: {{number_format($order->items[0]->price/$product->unit_price)}} SMS</span>
                        @endforeach
                    </span>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalDetail')" wire:loading.attr="disabled">
                {{ __('x') }}
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- Modal Upload -->
    <x-jet-dialog-modal wire:model="modalUpload">
        <x-slot name="title">
            {{ __('Upload Bukti Pembayaran') }}
        </x-slot>

        <x-slot name="content">
            <div class="col-span-6 sm:col-span-4 p-3">
                <x-jet-label for="team" value="{{ __('Unggah bukti pembayaran dapat mempercepat verifikasi pembayaran') }}" />
                <x-jet-label for="team" value="{{ __('Pastikan bukti pembayaran menampilkan:') }}" />
            </div>
            <div class="flex justify-between">
                <div class="col-span-6 sm:col-span-4 px-3 pb-2">
                    <x-jet-label for="currency" value="{{ __('Tanggal/Waktu Transfer') }}"  />
                    <label class="block font-medium text-gray-700 dark:text-slate-300 text-xs" for="currency">
                        contoh: tgl. {{date('d/m/Y')}} / jam {{date('H:s:i')}}
                    </label>
                    <br>
                    <x-jet-label for="currency" value="{{ __('Detail Penerima') }}" />
                    <label class="block font-medium text-gray-700 dark:text-slate-300 text-xs" for="currency">
                        contoh: Transfer ke Rekening PT TELIXCEL
                    </label>
                </div>
                <div class="col-span-6 sm:col-span-4 px-3">
                    <x-jet-label for="currency" value="{{ __('Status Berhasil') }}" />
                    <label class="block font-medium text-gray-700 dark:text-slate-300 text-xs" for="currency">
                        contoh: Transfer BERHASIL, Transaksi Sukses
                    </label>
                    <br>
                    <x-jet-label for="currency" value="{{ __('Jumlah Transfer') }}" />
                    <label class="block font-medium text-gray-700 dark:text-slate-300 text-xs" for="currency">
                        contoh: Rp 123.456,00
                    </label>
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4 p-3">
                @livewire('image-upload', ['model'=> 'order', 'model_id'=>$order->id])
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('modalUpload')" wire:loading.attr="disabled">
                {{ __('x') }}
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
