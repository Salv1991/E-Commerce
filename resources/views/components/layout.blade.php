<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>
        
        @vite('resources/css/app.css')
        
        @vite(['resources/js/app.js'])

    </head>
    <body class="font-sans antialiased bg-stone-100 dark:bg-black dark:text-white/50">
        @if(!($hideHeader ?? false))
            <x-nav.header />
        @endif
            {{ $slot }}
        <x-nav.footer />
    </body>
</html>
