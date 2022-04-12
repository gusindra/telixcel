<x-app-layout>
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Project') }}
            </h2>
        </div>
    </header>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-blue-100 border sm:rounded border-blue-500 text-blue-700 px-4 py-3 mb-4" role="alert">
                <p class="font-bold capitalize">{{$project->status}}</p>
                <p class="text-sm">Some additional text to explain said message.</p>
            </div>
            <div class="md:grid md:grid-cols-5 md:gap-6">
                <div class="md:col-span-4">
                    @livewire('project.edit', ['uuid'=>$project->id])
                </div>
                <div class="md:col-span-1 flex justify-between">
                    <div class="px-4 sm:px-0">
                        <h3 class="text-lg font-medium text-gray-900">Process</h3>

                        <ul class="mt-1 text-sm text-gray-600">
                            <li>The Project information.</li>
                        </ul>
                    </div>

                    <div class="px-4 sm:px-0">

                    </div>
                </div>
            </div>
        </div>
    </div>


</x-app-layout>
