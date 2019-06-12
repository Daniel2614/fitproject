@extends('layouts.app')

@section('contentheader_title')
    Detalles Nivel - N# {{ $level->id }}
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/bootstrap-datepicker3.min.css') }}">
@endpush

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Detalles del nivel</h2>
        </div>
        <div class="box-body p">
            <div class="tw-shadow tw-rounded-lg tw-overflow-hidden">
                <table class="table table-striped tw-table-auto tw-mb-0">
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Nombre</td>
                        <td class="tw-uppercase tw-text-xs">{{ $level->name }}</td>
                    </tr>
                </table>
            </div><!-- ./End tw-shadow-->
        </div><!-- ./End box body-->
        <a href="{{ route('levels.index') }}" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center">Volver</a>
    </div><!-- ./End box default-->
@endsection

@push('footer-scripts')
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.datepicker').datepicker({
                language: 'es',
                format: 'yyyy-mm-dd',
                orientation: 'bottom',
                startDate: new Date(),
                todayHighlight: true,
                autoclose: true,
            });
            $('.select2').select2();
        })
    </script>
@endpush

