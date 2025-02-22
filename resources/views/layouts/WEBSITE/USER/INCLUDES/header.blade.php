<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }} - {{ $title ?? null }}</title>

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

<!-- jQuery CDN -->
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<!-- Scripts -->
@vite(['resources/sass/app.scss', 'resources/js/app.js'])

{{-- SLICK CAROUSEL --}}
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />

<style>
    html,
    body {
        height: 100svh;
        overflow-y: scroll;
        scrollbar-width: none;
        /* For Firefox */
        margin: 0;/* Removes default margin */
        /* background: linear-gradient(#0000, #0010ff33),
            radial-gradient(circle, rgb(255, 15, 184) 0%, rgb(0, 0, 0) 100%);

        font-family: Arial, sans-serif; */
    }

    body::-webkit-scrollbar {
        display: none;
        /* For Chrome, Safari, and Edge */
    }

    #navbarSupportedContent a {
        font-size: 1.1rem;
        /* padding: 1.5rem; */
        font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
    }

    #navbarSupportedContent a:hover {
        color: goldenrod;
    }

    /* Hide the main content initially */
    #main-content {
        display: none;
        padding: 20px;
    }
</style>

@if (env('WEBSITE_ENABLE_LOADING_SCREEN'))
    <style>
        /* LOADING SCREEN START */
        /* styles.css */

        /* Full-screen overlay for the loading screen */
        #loading-screen {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: white;
            /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            /* Ensures it's on top of other content */
            color: white;
            font-family: Arial, sans-serif;
        }

        /* Image for the loading screen */
        #loading-screen img {
            max-width: 200px;
            /* Adjust the size of the image */
            width: 100%;
            /* Make sure the image is responsive */
            height: auto;
        }

        /* LOADING SCREEN END */
    </style>
@endif
@if (env('WEBSITE_ENABLE_DARK_LIGHT_FEATURE'))
    <style>
        #theme-toggle {
            color: #090909;
            /* padding: 0.7em 1.7em; */
            font-size: 18px;
            border-radius: 0.5em;
            /* background: #e8e8e8; */
            cursor: pointer;
            border: 1px solid #e8e8e8;
            transition: all 0.3s;
            text-align: center;
            /* box-shadow: 6px 6px 12px #c5c5c5, -6px -6px 12px #ffffff; */
        }

        #theme-toggle:active {
            color: #666;
            box-shadow: inset 4px 4px 12px #c5c5c5, inset -4px -4px 12px #ffffff;
        }
    </style>
@endif
<style>
    /* Enable hover effect */
    .nav-item.dropdown:hover .dropdown-menu {
        display: block;
    }

    /* Ensure smooth dropdown appearance */
    .nav-item.dropdown .dropdown-menu {
        margin-top: 0;
    }

    .nav-icon {
        font-size: 25px;
    }

</style>
