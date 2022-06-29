<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>500 Server Error</title>
    <link rel="stylesheet" href="{{ url('backend/css/app.css') }}">
</head>

<body>
<section>

<div class="bg-black text-white">
    <div class="flex h-screen">
        <div class="m-auto text-center">

            <p class="text-sm md:text-base text-yellow-300 p-2 mb-4">
                Please click button below to refresh this aplication
            </p>
            <a class="flex justify-center cursor-pointer bg-transparent hover:bg-gray-300 text-gray-300 hover:text-gray-800 rounded shadow hover:shadow-lg py-2 px-4 border border-gray-300 hover:border-transparent" href="{{url()->previous()}}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
                Reload
            </a>
        </div>
    </div>
</div>
</section>
</body>

</html>
