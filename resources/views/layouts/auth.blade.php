<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
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
