<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') - PGS Consulting</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col items-center justify-center p-6">
        <div class="max-w-md w-full text-center space-y-6">
            {{-- Application Logo --}}
            <div class="flex justify-center mb-8">
                <x-application-logo class="w-auto h-24" />
            </div>

            <h1 class="text-9xl font-black text-gray-200 dark:text-gray-800">
                @yield('code')
            </h1>

            <div>
                <h2 class="text-2xl font-bold uppercase tracking-tight">
                    @yield('message')
                </h2>
                <p class="mt-2 text-gray-500">
                    Mohon maaf atas ketidaknyamanannya. Sepertinya terjadi kesalahan atau halaman yang Anda cari tidak tersedia.
                </p>
            </div>

            <div class="pt-8">
                <a href="{{ url('/') }}" class="inline-block px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition transform hover:scale-105 shadow-lg shadow-blue-500/30">
                    Kembali ke Beranda
                </a>
            </div>

            <div class="pt-12 text-sm text-gray-400">
                &copy; {{ date('Y') }} PT. Pratama Global Sistem
            </div>
        </div>
    </body>
</html>