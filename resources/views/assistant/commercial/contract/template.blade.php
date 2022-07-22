<!-- Fonts -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
<!-- Styles -->
<link rel="stylesheet" href="{{ url('backend/css/app.css') }}">
<div>
    <x-slot name="header"></x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="container mx-auto" x-data="get_data()" >
                    <div class="px-4 py-2  " id="PRINT">
                        <!-- <div class="flex justify-between my-4">
                            <div class="justify-end flex p-4">
                                <div class="flex items-center justify-end px-2 text-right">
                                    <a href="{{ route('commercial.show', ['item']) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
                                        {{__('Print')}}
                                    </a>
                                </div>
                            </div>
                            <div class="justify-end flex p-4">
                                <div class="flex items-center justify-end px-2 text-right">
                                    <a href="{{ route('commercial.show', ['item']) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition" wire:click="actionShowModal">
                                        {{__('Print')}}
                                    </a>
                                </div>
                            </div>
                        </div> -->
                        <!-- Header -->
                        <div class="md:grid md:grid-cols-3 md:gap-6 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium text-gray-900">Logo</h3>
                                </div>
                            </div>
                            <div class="mt-5 md:mt-0 md:col-span-2 uppercase text-center">
                                CONFIDENTIAL<br>
                                CORPORATE QUOTATION - {{$data->title}}
                            </div>
                        </div>
                        <!-- Costumer -->
                        <div class="md:grid md:grid-cols-3 md:gap-6 my-4">
                            <div class="md:col-span-1 flex justify-between">
                                <div class="px-4 sm:px-0">
                                    <h3 class="text-lg font-medium text-gray-900">1. Customer Information :</h3>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> Up : </td>
                                                <td class="px-6 text-sm whitespace-no-wrap"> {{auth()->user()->currentTeam->name}} </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="px-6 text-sm whitespace-no-wrap"> Company : </td>
                                                <td class="px-6 text-sm whitespace-no-wrap"> {{auth()->user()->currentTeam->name}} </td>
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
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-2">Unit Measurement</th>
                                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider w-1/4">Desc</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach ($data->items as $item)
                                                    <tr class=" ">
                                                        <td class="px-6 py-4 text-sm whitespace-no-wrap"> {{$loop->iteration}} </td>
                                                        <td class="px-6 py-4 text-sm whitespace-no-wrap"> {{$item->name}} </td>
                                                        <td class="px-6 py-4 text-sm whitespace-no-wrap"> {{$item->price}} </td>
                                                        <td class="px-6 py-4 text-sm whitespace-no-wrap"> {{$item->unit}} </td>
                                                        <td class="px-6 py-4 text-sm whitespace-no-wrap"> {{$item->note}} </td>
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
                            <div class="mt-5 md:mt-0 md:col-span-3 text-center font-bold">
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
                                    <br>
                                    <br>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr class="border-none">
                                                <td class="  text-sm whitespace-no-wrap"> Name : </td>
                                                <td class="  text-sm whitespace-no-wrap"> {{$data->addressed_name}}</td> </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="  text-sm whitespace-no-wrap"> Position : </td>
                                                <td class="  text-sm whitespace-no-wrap"> {{$data->addressed_role}} </td>
                                            </tr>
                                            <tr class="border-none">
                                                <td class="  text-sm whitespace-no-wrap"> Date : </td>
                                                <td class="  text-sm whitespace-no-wrap"> {{$data->date->format('d M Y')}} </td>
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
