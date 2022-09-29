<div>
    <div class="col-span-12 sm:col-span-1 flex justify-between">
        <div>
            <x-jet-label for="filePath" value="{{ __('File Path') }}" />
            <x-jet-input id="filePath" type="text" class="mt-1 block w-full" wire:model="filePath" wire:model.defer="filePath" wire:model.debunce.800ms="filePath" />
            <x-jet-input-error for="filePath" class="mt-2" />
        </div>
        <!-- <x-jet-button class="ml-2" wire:click="updatePath" wire:loading.attr="disabled">
            {{ __('Go') }}
        </x-jet-button> -->
    </div>

    <table>
    </table>

    <div class="">
        <div class="container mx-auto w-full h-full">
            <div class="mx-auto w-full h-full flex flex-col items-center justify-center">
                <div x-data="dataTable()" x-init="
        initData()
        $watch('searchInput', value => {
          search(value)
        })" class=" dark:bg-slate-700 shadow-md w-full flex flex-col">
                    <div class="flex justify-between items-center">
                        <div>
                            <input x-model="searchInput" type="text" class="px-2 py-1 border rounded focus:outline-none dark:bg-slate-800" placeholder="Search...">
                        </div>
                    </div>
                    <table class="mt-5">
                        <thead class="border-b-2">
                            <th> <div class="flex space-x-2"> <span> Time </span> </div> </th>
                            <th> <div class="flex space-x-2"> <span> To </span> </div> </th>
                            <th> <div class="flex space-x-2"> <span> Data </span> </div> </th>
                            <th> <div class="flex space-x-2"> <span> User </span> </div> </th>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr x-show="checkView(index + 1)" class="hover:bg-gray-200 text-gray-900 text-xs">
                                    <td class="p-3">
                                        <span x-text="item.time"></span>
                                    </td>
                                    <td class="p-3">
                                        <span x-text="item.to"></span>
                                    </td>
                                    <td class="p-3">
                                        <span x-text="item.data"></span>
                                    </td>
                                    <td class="p-3">
                                        <span x-text="item.user"></span>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="isEmpty()">
                                <td colspan="5" class="text-center py-3 text-gray-900 text-sm">No matching records found.</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex mt-5">
                        <div class="border px-2 cursor-pointer" @click.prevent="changePage(1)">
                            <span class="text-gray-700">First</span>
                        </div>
                        <div class="border px-2 cursor-pointer" @click="changePage(currentPage - 1)">
                            <span class="text-gray-700"><</span>
                        </div>
                        <template x-for="item in pages">
                            <div @click="changePage(item)" class="border px-2 cursor-pointer" x-bind:class="{ 'bg-gray-300': currentPage === item }">
                                <span class="text-gray-700" x-text="item"></span>
                            </div>
                        </template>
                        <div class="border px-2 cursor-pointer" @click="changePage(currentPage + 1)">
                            <span class="text-gray-700">></span>
                        </div>
                        <div class="border px-2 cursor-pointer" @click.prevent="changePage(pagination.lastPage)">
                            <span class="text-gray-700">Last</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/fuse.js/dist/fuse.js"></script>
    <script>
        // let data = [
        //     // {"name":"Brielle Kuphal","email":"Brielle31@gmail.com","job":"Global Metrics Developer","country":"Tunisia","year":1969},
        //     // {"name":"Barney Murray","email":"Barney75@gmail.com","job":"Product Solutions Executive","country":"Haiti","year":1970},
        //     {"name":"Ressie Ruecker","email":"Ressie.Ruecker30@gmail.com","job":"Forward Factors Orchestrator"},
        //     {"name":"Teresa Mertz","email":"Teresa_Mertz@hotmail.com","job":"Dynamic Division Director"},
        // ]
        // console.log(data);
        @if($logs)
            var response = @json($logs, JSON_PRETTY_PRINT);
            response = response.replace(/\n /g, '');
            let tdata = JSON.parse(response);
        @endif
        console.log(tdata);

    </script>
    <script>
        window.dataTable = function() {
            return {
                items: [],
                view: 5,
                searchInput: '',
                pages: [],
                offset: 5,
                pagination: {
                    total: tdata.length,
                    lastPage: Math.ceil(tdata.length / 5),
                    perPage: 5,
                    currentPage: 1,
                    from: 1,
                    to: 1 * 5
                },
                currentPage: 1,
                sorted: {
                    field: 'time',
                    rule: 'asc'
                },
                initData() {
                    this.items = tdata
                    this.showPages()
                },
                checkView(index) {
                    return index > this.pagination.to || index < this.pagination.from ? false : true
                },
                checkPage(item) {
                    if (item <= this.currentPage + 5) {
                        return true
                    }
                    return false
                },
                search(value) {
                    if (value.length > 1) {
                        const options = {
                            shouldSort: false,
                            keys: ['time', 'to', 'user'],
                            threshold: 0
                        }
                        const fuse = new Fuse(tdata, options)
                        this.items = fuse.search(value).map(elem => elem.item)
                    } else {
                        this.items = tdata
                    }
                    // console.log(this.items.length)

                    this.changePage(1)
                    this.showPages()
                },
                changePage(page) {
                    if (page >= 1 && page <= this.pagination.lastPage) {
                        this.currentPage = page
                        const total = this.items.length
                        const lastPage = Math.ceil(total / this.view) || 1
                        const from = (page - 1) * this.view + 1
                        let to = page * this.view
                        if (page === lastPage) {
                            to = total
                        }
                        this.pagination.total = total
                        this.pagination.lastPage = lastPage
                        this.pagination.perPage = this.view
                        this.pagination.currentPage = page
                        this.pagination.from = from
                        this.pagination.to = to
                        this.showPages()
                    }
                },
                showPages() {
                    const pages = []
                    let from = this.pagination.currentPage - Math.ceil(this.offset / 2)
                    if (from < 1) {
                        from = 1
                    }
                    let to = from + this.offset - 1
                    if (to > this.pagination.lastPage) {
                        to = this.pagination.lastPage
                    }
                    while (from <= to) {
                        pages.push(from)
                        from++
                    }
                    this.pages = pages
                },
                changeView() {
                    this.changePage(1)
                    this.showPages()
                },
                isEmpty() {
                    return this.pagination.total ? false : true
                }
            }
        }
    </script>
</div>
