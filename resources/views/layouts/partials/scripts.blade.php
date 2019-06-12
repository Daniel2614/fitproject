{{-- Main js --}}
<script src="{{ mix('js/app.js') }}"></script>
<script src="{{asset('plugins/toastr/toastr.js')}}"></script>

{{-- Slimscroll is required when using the fixed layout. --}}
<script src="{{ asset('plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>

<script>
    toastr.options = {
        "closeButton": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "timeOut": "5000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    $(window).on('load', function(){
        @if(Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            switch( type ) {
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif
    })
</script>
@stack('footer-scripts')
