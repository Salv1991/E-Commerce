<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>
        <!-- Fonts -->
        <!-- <link rel="preconnect" href="https://fonts.bunny.net"> -->
        <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" /> -->
        @vite('resources/css/app.css')
        
        @vite(['resources/js/app.js'])

    </head>
    <body class="font-sans antialiased bg-stone-100 dark:bg-black dark:text-white/50">
        <x-nav.header  />
            {{ $slot }}
        <x-nav.footer/>
    </body>
</html>
