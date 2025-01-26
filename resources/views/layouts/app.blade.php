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
    <!-- Navbar -->
    <header>
        @include('components.navbar') <!-- Include Navbar -->
    </header>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    <!-- Footer -->
    <footer>
        @include('components.footer') <!-- Include Footer -->
    </footer>
    
    @livewireScripts
</body>

</html>