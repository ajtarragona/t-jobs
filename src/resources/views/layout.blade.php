<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="base-url" content="{{ url('') }}">
        <meta name="lang" content="{{ app()->getLocale() }}">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TGN Jobs - @yield('title') </title>

        <link href="{{ asset('vendor/ajtarragona/css/tjobs.css') }}" rel="stylesheet">
        
        @yield('css')
	    @yield('meta')
	    @yield('head-js')

    </head>
    <body class="h-100 overflow-hidden">
        <div class="h-100">

            <div class="row g-0 h-100">
                <div class="col-sm-2   " >
                    <div class="h-100 border-end" style="overflow-y: auto">
                        Menú
                    </div>
                </div>
                <div class="col-sm-10 h-100 bg-secondary  bg-opacity-10">
                    <div class="h-100 ">
                        
                        contingut
                        @yield('body')

                    </div>
                </div>
            </div>
        </div>

        @yield('pre-js')
        <script src="{{ asset('vendor/ajtarragona/js/tjobs.js')}}" language="JavaScript"></script>
        @yield('js')
        
    </body>

</html>