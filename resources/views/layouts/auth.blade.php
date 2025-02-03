<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Livewire Component</title>
    @livewireStyles
</head>

<body>
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    <!-- Footer -->
    @livewireScripts
</body>

</html>
