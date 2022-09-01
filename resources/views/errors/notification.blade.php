<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>404 Error Page</title>
    <link rel="stylesheet" href="{{ url('backend/css/app.css') }}">
</head>

<body>
    <section>

        <div class="bg-black text-white">
            <div class="flex h-screen">
                <div class="m-auto text-center">

                    <p class="text-sm md:text-base text-yellow-300 p-2 mb-4">
                        The stuff you were looking for doesn't exist
                    </p>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
