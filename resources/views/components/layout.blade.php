<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel Commerce</title>      
        @vite(['resources/css/app.css', 'resources/js/app.js'])


    </head>
    <body class="font-sans antialiased bg-white dark:bg-black dark:text-white/50 wrapper">
        @if(!($hideHeader ?? false))
            <x-nav.header />
        @endif
        
        <main>
            {{ $slot }}
        </main>

        @if(!($hideFooter ?? false))
            <x-nav.footer />
        @endif
    </body>
</html>
