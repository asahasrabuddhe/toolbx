<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield('title')</title>
        <link rel="icon" href="" type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
        @section('styles')
        @show
    </head>
    @if( isset($bodyClass) )
        <body class="{{ $bodyClass }}">
    @else
        <body>
    @endif
    @section('content')
    @show
    @include('includes.footer')
    <script src="{{ asset('/js/app.js') }}"></script>
    @section('scripts-top')
    @show
    @section('scripts')
    @show
    </body>
</html>
