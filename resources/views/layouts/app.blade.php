<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Livewire Component</title>
    @livewireStyles
    @livewireScripts
</head>

<body>
    <!-- Navbar -->
    <livewire:components.navbar /> <!-- Include Navbar -->
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    <!-- Footer -->
    <livewire:components.footer /> <!-- Include Footer -->
    
</body>

</html>
