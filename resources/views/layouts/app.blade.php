@vite('resources/css/app.css')

<body class="text-white min-h-screen w-full bg-cover bg-center bg-no-repeat flex flex-col" style="background-image: url('/images/fantastic_library.jpg')">
    <x-navbar />
    <main class="flex-1 flex flex-col items-center w-full">
        @yield('content')
    </main>
    <footer class="w-full text-center text-gray-300 py-4 text-sm">
        Copyright &copy; {{ date('Y') }} Michael Baggott
    </footer>
</body>
