<!DOCTYPE html>
<html lang="en">
    @include('layouts.partials.htmlheader')
    <body class="hold-transition tw-px-4">
        <div id="app" class="tw-min-h-screen tw-flex tw-flex-col tw-items-center tw-justify-center">
            <div class="login-logo tw-mb-4">
                <a class="tw-font-bold" href="{{ url('/') }}">
                    <span class="tw-text-grey-darkest">Fit</span><span class="tw-font-normal tw-text-black">Project<span>
                </a>
            </div><!-- /.login-logo -->
            @yield('content')
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
