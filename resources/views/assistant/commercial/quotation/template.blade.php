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
                    <a href="{{route('commercial.edit.show', ['key'=>'quotation','id'=>$data->id])}}" class="text-xs bg-opacity-50 bg-gray-100 text-gray-700 px-2 py-1 rounded-sm hover:bg-gray-300 flex items-center">Back</a>
                    <button type="button" x-on:click="printDiv()" class="text-xs bg-opacity-50 hover:bg-gray-300 text-gray-700 shadow-sm border px-2 py-1 rounded-sm bg-white flex items-center"><svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-1 h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>Print</button>
                </div>
            @endisset
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="container mx-auto" x-ref="print" >
                    <div class="px-4 py-2" id="PRINT">
                        <!-- Header -->
                        <!--<div class="md:grid md:grid-cols-3 md:gap-6 my-4">-->
                        <!--    <div class="md:col-span-1 flex justify-between">-->
                        <!--        <div class="px-4 sm:px-0">-->
                        <!--            @if($data->model == 'COMPANY')-->
                        <!--                @if($data->company && $data->company->img_logo)-->
                        <!--                <img style="height:100px;" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$data->company->img_logo->file}}" />-->

                        <!--                @endif-->
                        <!--            @elseif($data->model == 'PROJECT')-->
                        <!--                @if($data->project->company && $data->project->company->img_logo)-->
                        <!--                <img style="height:100px;" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$data->project->company->img_logo->file}}" />-->
                        <!--                @endif-->
                        <!--            @else-->
                        <!--                <h3 class="text-lg font-medium text-gray-900">{{$data->company->name}}</h3>-->
                        <!--            @endif-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--    <div class="mt-5 md:mt-0 md:col-span-2 uppercase text-center">-->
                        <!--        CONFIDENTIAL<br>-->
                        <!--        CORPORATE QUOTATION - {{$data->title}}-->
                        <!--    </div>-->
                        <!--</div>-->
                        <table class="min-w-full divide-y divide-gray-200">
                            <tr>
                                <td>
                                    <div class="px-4 sm:px-0">
                                        @if($data->model == 'COMPANY')
                                            @if($data->company && $data->company->img_logo)
                                            <img style="height:100px;" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$data->company->img_logo->file}}" />

                                            @endif
                                        @elseif($data->model == 'PROJECT')
                                            @if($data->project->company && $data->project->company->img_logo)
                                            <img style="height:100px;" src="https://telixcel.s3.ap-southeast-1.amazonaws.com/{{$data->project->company->img_logo->file}}" />
                                            @endif
                                        @else
                                            <h3 class="text-lg font-medium text-gray-900">{{$data->company->name}}</h3>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="mt-5 md:mt-0 md:col-span-2 uppercase text-left">
                                        <strong>CONFIDENTIAL<br>
                                        CORPORATE QUOTATION - {{$data->title}}</strong>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <!-- Costumer -->
                        <div class="md:grid md:grid-cols-3 md:gap-6 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium text-gray-900">1. Customer Information :</h3>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> Up : </td>
                                                <td class="px-6 text-sm whitespace-no-wrap"> {{$data->addressed_name}} </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> Company : </td>
                                                <td class="px-6 text-sm whitespace-no-wrap"> {{$data->addressed_company}} </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> Quote No : </td>
                                                <td class="px-6 text-sm whitespace-no-wrap"> {{$data->quote_no}} </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> Date : </td>
                                                <td class="px-6 text-sm whitespace-no-wrap"> {{$data->date->format('d M Y')}} </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-5 md:mt-0 md:col-span-2"> </div>
                        </div>
                        <!-- Service -->
                        <div class="md:grid md:grid-cols-3 md:gap-6 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium text-gray-900">2. Service Description :</h3>
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
                                    <h3 class="text-lg font-medium text-gray-900">3. Offering Price :</h3>
                                </div>
                            </div>

                            <div class=" md:mt-0 md:col-span-3">
                                <div class=" bg-white  shadow sm:rounded-tl-md sm:rounded-tr-md">
                                    <div class="gap-6">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead>
                                                <tr>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1">No</th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/2">Item</th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1">Price (IDR)</th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-2">Unit</th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Desc</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($data->items as $item)
                                                    <tr class=" ">
                                                        <td class="px-6 py-2 text-sm whitespace-no-wrap align-top"> {{$loop->iteration}} </td>
                                                        <td class="px-6 py-2 text-sm whitespace-no-wrap align-top"> {{$item->name}} </td>
                                                        <td class="px-2 py-2 text-sm whitespace-no-wrap text-right align-top"> {{number_format($item->price)}} </td>
                                                        <td class="px-2 py-2 text-sm whitespace-no-wrap flex"> {{$item->qty>0 ? $item->qty:''}} {{$item->unit}} </td>
                                                        <td class="px-6 py-2 text-sm whitespace-no-wrap align-top"> {{$item->note}} </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Duration -->
                        <div class="md:grid md:grid-cols-3 md:gap-6 my-4 p-3 border">
                            <div class="md:col-span-3 text-center font-bold">
                                This Quotation Valid for {{$data->valid_day}} days
                            </div>
                        </div>
                        <!-- Terms -->
                        <div class="md:grid  md:gap-6 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium text-gray-900">Terms & Conditions :</h3>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> {!! nl2br($data->terms)!!}</td> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-5 md:mt-0 md:col-span-2"> </div>
                        </div>
                        <!-- Footer -->
                        <div class="flex justify-between my-4">
                            <div class="justify-end flex p-4">
                                <div class="  items-center justify-end px-2  ">
                                    <p class="text-gray-900">Prepare By</p>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <p class="text-gray-900">{{$data->created_by}}</p>
                                    <p class="text-gray-900">{{$data->created_role}}</p>

                                </div>
                            </div>
                            <div class="justify-end flex p-4">
                                <div class="  items-center justify-end px-2 text-right">
                                    <p class="text-gray-900">I, hereby accept the quotation</p>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="text-base whitespace-no-wrap"> Name </td>
                                                <td class="text-base whitespace-no-wrap"> </td> </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="text-base whitespace-no-wrap"> Position </td>
                                                <td class="text-base whitespace-no-wrap">  </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="text-base whitespace-no-wrap"> Date </td>
                                                <td class="text-base whitespace-no-wrap">  </td>
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
