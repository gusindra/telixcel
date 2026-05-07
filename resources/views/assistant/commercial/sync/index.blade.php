<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>

    <x-slot name="header"></x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-600 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="container mx-auto" x-data="selectAllData()">
                    <div class="flex justify-between">
                        <div class="p-4">
                            <a href="/get-from-ginee" value="Import" class="px-2 py-1 border border-1 cursor-pointer">Syn</a>
                            <form method="post" class="flex" >
                                @csrf
                                <div class="grid">
                                    <label for="field">Update Field:</label>
                                    <select class="dark:text-slate-500" name="field[]" id="field" multiple required>
                                        <option value="stock">Avaiable Stock</option>
                                        <option value="dimensions">Dimentions</option>
                                        <option value="price">Price</option>
                                    </select>
                                </div>
                                <div class="grid">
                                    <label for="field">Select Syn ID:</label>
                                    <textarea class="dark:text-slate-500" type="text" :value="ids.join(', ')" name="group_id" required></textarea>
                                </div>
                                <input type="submit" value="Import" class="px-2 py-1 border border-1 cursor-pointer" />
                            </form>
                        </div>
                    </div>

                    <div class="px-4 py-1">
                        <div class="flex flex-col">
                            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="overflow-hidden">
                                        <table class="min-w-full table">
                                            <thead class="bg-white border-b">
                                                <tr>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        <input x-on:click="selectAll" type="checkbox" id="checkall">
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Product Name, SKU, Json Data
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Bind Product
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Status
                                                    </th>
                                                    <th scope="col" class="text-sm font-medium text-gray-900 px-6 py-4 text-left">
                                                        Info
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($syns as $syn)
                                                    <tr class="bg-white border-b transition duration-300 ease-in-out hover:bg-gray-100">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            <input {{$syn->product ? '' : 'disabled'}} x-model="ids" type="checkbox" value="{{$syn->id}}" class="item_check">
                                                        </td>
                                                        <td class="text-xs text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                            <span class="font-bold">{{$syn->name}}</span><br><a href="#">{{$syn->sku}}</a><br>
                                                            @foreach (json_decode($syn->details) as $key => $item)
                                                                {{$key}} :
                                                                @if(is_object($item))
                                                                    @foreach ($item as $key1 => $i)
                                                                    <br>{{$key1}} : {{ json_encode($i)}}
                                                                    @endforeach
                                                                @else
                                                                {{json_encode($item)}}
                                                                @endif
                                                                <br>
                                                            @endforeach
                                                        </td>
                                                        <td class="text-xs text-gray-900 font-light px-6 py-4 whitespace-nowrap">
                                                            @if($syn->product)
                                                                @foreach (json_decode($syn->product) as $key => $pro)
                                                                    <br>{{$key}} : {{$pro}}
                                                                @endforeach
                                                            @endif
                                                            <br>
                                                            @if(@$syn->product->stock)
                                                                @foreach (json_decode($syn->product->stock) as $key => $pro)
                                                                    <br>{{$key}} : {{$pro}}
                                                                @endforeach
                                                            @endif
                                                        </td>
                                                        <td class="text-xs text-gray-900 font-light px-6 py-4 whitespace-nowrap">{{$syn->status}}</td>
                                                        <td class="text-xs text-gray-900 font-light px-6 py-4 whitespace-nowrap">@if($syn->status!='new'){{$syn->info}}<br>{{$syn->updated_at->format('d M Y H:i:s')}}@endif</td>
                                                    </tr>
                                                @endforeach
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
    <script type="module">
        import {DataTable} from "../../dist/module.js"
        const table = new DataTable("table")
    </script>
    <script>
        function selectAllData() {
            return {
                ids: [],
                selectall: false,
                checkedNames: [],
                allNames: [{{$syns->pluck('id')}}],
                selectAll() {
                    this.selectall = !this.selectall
                    if(this.selectall==true) {
                        this.ids = this.allNames
                    }else{ this.ids = [] }
                }
            }
        }
    </script>
</x-app-layout>
