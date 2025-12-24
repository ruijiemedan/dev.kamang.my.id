<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MikroTik Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind CDN (Laravel 12 friendly) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <nav class="bg-gray-900 text-white px-6 py-4 flex justify-between">
        <div class="font-bold text-lg">MikroTik Monitor</div>
        <div class="text-sm">API 151.242.116.206</div>
    </nav>

    <main class="p-6">
        @yield('content')
    </main>

</body>
</html>
