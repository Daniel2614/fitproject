<head>
    <meta charset="UTF-8">
    <title> FitProject</title>
        <link rel="icon" href="{{ URL::asset('/public/img/gim.png') }}">

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Fonts--}}
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700|Open+Sans:600,700" rel="stylesheet">

    {{-- Font Awesome Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

<script src="{{ asset('/plugins/Font_awesome/js/all.js') }}"></script>

    {{-- Theme styles --}}
    <script src="{{ asset('js/pace.min.js') }}"></script>
    <link href="{{ asset('/css/skins/skin-black.css') }}" rel="stylesheet" type="text/css" />
    @stack('styles')
    @stack('header-scripts')
    <link href="{{ asset('/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

