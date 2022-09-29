<!-- Fonts -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
<!-- Styles -->
<link rel="stylesheet" href="{{ url('backend/css/app.css') }}">
<style>
    body{
        padding: 0 20px;
    }
    @media print {
        html,body{height:100%;width:100%;margin:0;padding: 0;font-size: small;}
        @page {
            size: A4 potrait;
            max-height:100%;
            max-width:100%
        }
        img {
            width:auto;
            height:100%;
            display:block;
        }
    }
</style>
<script src="//unpkg.com/alpinejs" defer></script>
<div>
    <x-slot name="header"></x-slot>

    <div>
        <div x-data="{
            printDiv() {
                var printContents = this.$refs.print.innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            }
        }"
        x-cloak
        x-ref="container"
        class="print:text-black relative">
        @isset($printButton)
            {{ $printButton }}
        @else
            <div class="print:hidden sticky top-1 right-1 left-1 p-2 flex text-right justify-between">
                <a href="{{route('invoice')}}" class="text-xs bg-opacity-50 bg-gray-100 text-gray-700 px-2 py-1 rounded-sm hover:bg-gray-300 flex items-center">Back</a>
                <button type="button" x-on:click="printDiv()" class="text-xs bg-opacity-50 hover:bg-gray-300 text-gray-700 shadow-sm border px-2 py-1 rounded-sm bg-white flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-1 h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>Print</button>
            </div>
        @endisset
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div x-ref="print" class="container mx-auto">
                    <div id="PRINT" class="text-xs">
                        <!-- Header -->
                        <div class="md:grid md:grid-cols-3 md:gap-6 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    @if($data->company && $data->company->img_logo)
                                    <img style="height:100px;" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$data->company->img_logo->file}}" />
                                    @else
                                    <h3 class="text-lg font-medium text-gray-900">{{$data->company ? $data->company->name : $data->entity_party}}</h3>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <!-- Costumer -->
                        <div class="md:grid md:grid-cols-1 md:gap-6 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="text-base whitespace-no-wrap"> Invoice No </td>
                                                <td class="pl-6">:</td>
                                                <td class="text-base whitespace-no-wrap"> {{$data->no}} </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="text-base whitespace-no-wrap"> Invoice Date </td>
                                                <td class="pl-6">:</td>
                                                <td class="text-base whitespace-no-wrap"> {{$data->date->format("d F Y")}} </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="text-base whitespace-no-wrap"> Due Date </td>
                                                <td class="pl-6">:</td>
                                                <td class="text-base whitespace-no-wrap"> {{$data->date->addDays(30)->format("d F Y")}} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200 my-4">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="pr-52">
                                        <h3 class="text-lg font-medium text-gray-900">Bill to :</h3>
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap font-bold uppercase"> {{@$data->customer->name}} </td>
                                                </tr>
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap"> {{@$data->customer->address}} </td>
                                                </tr>
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap"> NPWP : {{@$data->customer->user->userBilling->tax_id}} </td>
                                                </tr>
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap"> CN : {{@$data->customer->uuid}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td class="align-top">
                                        <h3 class="text-lg font-medium text-gray-900">From :</h3>
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap"> {{$data->company ? $data->company->name : $data->entity_party}} </td>
                                                </tr>
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap"> {{$data->company ? $data->company->address : $data->entity_party}} </td>
                                                </tr>
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap"> NPWP : {{@$data->company->tax_id}} </td>
                                                </tr>
                                                <tr class="border-none">
                                                    <td class="text-sm whitespace-no-wrap"> Attachment : 1</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                        <!-- Service -->
                        <div class="mt-5 md:mt-0 md:col-span-2 text-xl uppercase text-center">
                            INVOICE
                        </div>
                        <div class="hidden">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium text-gray-900">Term :</h3>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> {!! nl2br($data->description)!!}</td> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-5 md:mt-0 md:col-span-2"> </div>
                        </div>
                        <!-- Offering -->
                        <div class="md:grid md:grid-cols-3 md:gap-2 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                </div>
                            </div>

                            <div class="md:mt-0 md:col-span-3">
                                <div class="bg-white shadow sm:rounded-tl-md sm:rounded-tr-md">
                                    <div class="gap-6">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr class="text-center">
                                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1">No</th>
                                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/3">Description</th>
                                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1">Price (IDR)</th>
                                                    <th class="px-3 py-2 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1">Qty</th>
                                                    <th class="w-1"></th>
                                                    <th class="px-3 py-2 bg-gray-50 text-right text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200 text-xs">
                                                @foreach ($data->items as $item)
                                                    <tr class=" ">
                                                        <td class="px-3 py-1 whitespace-no-wrap"> {{$loop->iteration}} </td>
                                                        <td class="px-3 py-1 whitespace-no-wrap"> {{$item->name}}<br><small>{{$item->note}}</small> </td>
                                                        <td class="px-3 py-1 whitespace-no-wrap text-right"> {{number_format($item->price)}} </td>
                                                        <td class="px-3 py-1 whitespace-no-wrap"> {{number_format($item->qty)}} {{$item->unit}} </td>
                                                        <td class="px-3 py-1 whitespace-no-wrap text-center"> {{$item->total_percentage==100?'':'('.$item->total_percentage.'%)'}}</td>
                                                        <td class="px-3 py-1 whitespace-no-wrap text-right"> {{$item->total_percentage==100?number_format($item->qty*$item->price):number_format($item->qty*$item->price*$item->total_percentage/100)}} </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-gray-100">
                                                    <td class="px-1 py-2 text-sm whitespace-no-wrap text-right" colspan="5" align="center">Sub Total</td>
                                                    <td class="px-2 py-2 text-sm whitespace-no-wrap text-right"><span class="uppercase">{{$data->bill->currency ?? 'IDR'}}</span> {{number_format($data->total)}}</td>
                                                </tr>
                                                @if($data->vat>0)
                                                <tr class="bg-gray-100">
                                                    <td class="px-1 py-2 text-xs whitespace-no-wrap text-right" colspan="5" align="center">VAT {{'('.$data->vat.'%)'}}</td>
                                                    <td class="px-2 py-2 text-xs whitespace-no-wrap text-right"><span class="uppercase">{{$data->bill->currency ?? 'IDR'}}</span> {{number_format($data->bill->amount*($data->vat/100))}}</td>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    <td class="px-1 py-2 text-xl whitespace-no-wrap text-right" colspan="5" align="center">Total</td>
                                                    <td class="px-2 py-2 text-xl whitespace-no-wrap text-right"><span class="uppercase">{{$data->bill->currency ?? 'IDR'}}</span> {{number_format($data->bill->amount + $data->bill->amount*($data->vat/100))}}</td>
                                                </tr>
                                                @else
                                                <tr class="bg-gray-100">
                                                    <td class="px-1 py-2 text-xl whitespace-no-wrap text-right" colspan="5" align="center">Total</td>
                                                    <td class="px-2 py-2 text-xl whitespace-no-wrap text-right"><span class="uppercase">{{$data->bill->currency ?? 'IDR'}}</span> {{number_format($data->bill->amount)}}</td>
                                                </tr>
                                                @endif
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Duration -->
                        <div class="md:grid md:grid-cols-3 my-4 p-3 border">
                            <div class="md:mt-0 md:col-span-3">
                                <strong class="">Description:</strong><br>
                                <span class="font-bold">{{$data->bill->description}}</span>
                            </div>
                        </div>
                        <!-- Terms -->
                        <div>
                            <div>
                                <table class="min-w-full divide-y divide-gray-200">
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr class="border-none">
                                            <td class="text-sm whitespace-no-wrap align-top">
                                                <h3 class="text-sm font-medium text-gray-900">PLEASE REMIT PAYMENT DIRECTLY TO</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-bold text-sm align-top flex justify-around1 justify-end">
                                                <div class="border border-gray-50 p-2 mb-2">
                                                    @if($data->company && $data->company->payable)
                                                        @foreach ($data->company->payable as $account)
                                                            {{$account->provider_name}}<br>
                                                            {{$account->provider_location}}<br>
                                                            {{$account->account_name}}<br>
                                                            Account No {{$account->account_number}}<br>
                                                            {{$account->notes}}
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="md:col-span-2 border my-4 p-2 w-2/5">
                                <label>Note:</label>
                                <p contenteditable="true">{{$data->bill->note}}</p>
                            </div>
                        </div>
                        <!-- Footer -->
                        <div class="flex justify-between my-4">
                            <div class="justify-end flex p-4">
                                <div class="  items-center justify-end px-2  ">
                                    <p class="text-gray-900"></p>
                                </div>
                            </div>
                            <div class="justify-end flex p-4">
                                <div class="items-center justify-end px-2 text-right">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="font-bold text-sm whitespace-no-wrap"> {{@$data->company->person_in_charge}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
