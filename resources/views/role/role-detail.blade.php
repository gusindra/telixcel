<x-app-layout>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Role') }} : {{$role->name}}
            </h2>
        </div>
    </header>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            @livewire('role.edit', ['uuid'=>$role->id])
        </div>
    </div>


</x-app-layout>
