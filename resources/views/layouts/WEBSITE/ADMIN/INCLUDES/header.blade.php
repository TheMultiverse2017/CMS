<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }}</title>

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

<!-- jQuery CDN -->
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}

<!-- Scripts -->
@vite(['resources/sass/app.scss', 'resources/js/app.js'])

{{-- SLICK CAROUSEL --}}
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>
    .nav-icon {
        font-size: 25px;
    }

    .nav-item :hover {
        background-color: red;
        border-radius: 10px;
        /* Adjust the value for more or less rounding */
        transition: background-color 0.3s ease-in-out, border-radius 0.3s ease-in-out;
    }


    html,
    body {
        height: 100svh;
        overflow-y: scroll;
        scrollbar-width: none;
        /* For Firefox */
        margin: 0;/* Removes default margin */
    }

    .analyticsSmallCardBody {
        text-align: center;
        font-weight: 900;
        font-size: 4em;
    }

    /* .cardTotalTimeSpentDevice {
        -webkit-box-shadow: 0px 0px 16px 7px rgba(255, 46, 116, 0.27);
        -moz-box-shadow: 0px 0px 16px 7px rgba(255, 46, 116, 0.27);
        box-shadow: 0px 0px 16px 7px rgba(255, 46, 116, 0.27);
    }

    .cardTotalDeviceCount {
        -webkit-box-shadow: 0px 0px 16px 7px rgba(46, 164, 255, 0.27);
        -moz-box-shadow: 0px 0px 16px 7px rgba(46, 164, 255, 0.27);
        box-shadow: 0px 0px 16px 7px rgba(46, 164, 255, 0.27);
    }

    .cardUrlVisitDuration {
        -webkit-box-shadow: 0px 0px 16px 7px rgba(81, 255, 46, 0.27);
        -moz-box-shadow: 0px 0px 16px 7px rgba(81, 255, 46, 0.27);
        box-shadow: 0px 0px 16px 7px rgba(81, 255, 46, 0.27);
    }

    .visits7D {
        -webkit-box-shadow: 0px 0px 41px 20px rgba(255, 20, 67, 0.27);
        -moz-box-shadow: 0px 0px 41px 20px rgba(255, 20, 67, 0.27);
        box-shadow: 0px 0px 41px 20px rgba(255, 20, 67, 0.27);
    }

    .visits30D {
        -webkit-box-shadow: 0px 0px 41px 20px rgba(189, 171, 51, 0.27);
        -moz-box-shadow: 0px 0px 41px 20px rgba(189, 171, 51, 0.27);
        box-shadow: 0px 0px 41px 20px rgba(189, 171, 51, 0.27);
    }

    .visits60D {
        -webkit-box-shadow: 0px 0px 41px 20px rgba(68, 113, 227, 0.27);
        -moz-box-shadow: 0px 0px 41px 20px rgba(68, 113, 227, 0.27);
        box-shadow: 0px 0px 41px 20px rgba(68, 113, 227, 0.27);
    }

    .visits1Yr {
        -webkit-box-shadow: 0px 0px 41px 20px rgba(26, 110, 9, 0.27);
        -moz-box-shadow: 0px 0px 41px 20px rgba(26, 110, 9, 0.27);
        box-shadow: 0px 0px 41px 20px rgba(26, 110, 9, 0.27);
    } */
</style>
