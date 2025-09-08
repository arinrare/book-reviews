<!doctype html>
@vite('resources/css/app.css')
<head>
    <meta property="og:title" content="Michael's Book Reviews"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="https://michaelbaggott.site/bookreviewslaravel/"/>
    <meta property="og:image" content="https://michaelbaggott.site/bookreviewslaravel/images/banner.jpg"/>
    <meta property="og:site_name" content="Portfolio of Michael Baggott"/>
    <meta property="og:description" content="A fantasy/science fiction book review website built in Laravel, that uses Wordpress as a CMS. Designed for my portfolio, but kept up to date with books currently being read by Michael."/>
</head>


<body class="text-white min-h-screen w-full bg-cover bg-center bg-no-repeat flex flex-col" style="background-image: url('{{ asset('images/fantastic_library.jpg') }}')">
    <x-navbar />
    <main class="flex-1 flex flex-col items-center w-full">
        @yield('content')
    </main>
    <footer class="w-full text-center text-gray-300 py-4 text-sm">
        Copyright &copy; {{ date('Y') }} Michael Baggott
    </footer>
</body>
