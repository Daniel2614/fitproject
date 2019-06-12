<!DOCTYPE html>
<html lang="es">
@include('layouts.partials.htmlheader')
<body class="skin-black fixed tw-font-sans tw-antialiased tw-text-black tw-leading-tight">
    <div class="wrapper" id="app">
        <div id="loader">
            <img src="{{ asset('/img/rolling.svg') }}" alt="loader">
        </div>
        @include('layouts.partials.mainheader')
        @if( auth()->user()->isAdmin())
            @include('layouts.partials.sidebar')
        @else
            @include('layouts.partials.sidebar-customer')
        @endif
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <div class="tw-container tw-mx-auto tw-px-4 tw-pt-6">
                @include('layouts.partials.contentheader')
                <!-- Main content -->
                <section class="content tw-px-0 tw-pt-0">
                    <!-- Your Page Content Here -->
                    @yield('main-content')
                </section><!-- /.content -->
            </div>
        </div><!-- /.content-wrapper -->
        @include('layouts.partials.footer')
    </div><!-- ./wrapper -->
    @include('layouts.partials.scripts')
</body>
</html>
