@extends('layouts.app')

@section('contentheader_title')
    Editanto Nivel <span class="tw-font">N# {{ $level->id  }}</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/bootstrap-datepicker3.min.css') }}">
@endpush

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Actualizar nivel</h2>
        </div>
        <div class="box-body">
            <form action="{{ route('admin.levels.update', $level) }}" method="post" autocomplete="off">
                @csrf
                @method('PATCH')
                <div class="form-group {{ $errors->has('name') ? ' has-error': '' }}">
                    <label>Nombre</label>
                    <input class="form-control" type="text" name="name" value="{{ old('name', $level->name) }}">
                    @if ($errors->has('name'))
                        <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <button type="submit" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center">
                    <i class="fa fa-save tw-mr-1 tw-text-base"></i>
                    Guardar
                </button>
            </form>
        </div>
    </div><!-- ./End box default-->
@endsection

@push('footer-scripts')
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>
    <script>
        $(document).ready( function (e) {
            //Date TimePickers
            $('#start-datepicker').datepicker({
                language: 'es',
                format: 'yyyy-mm-dd',
                orientation: 'bottom',
                todayHighlight: true,
                autoclose: true,
            });

            $('#end-datepicker').datepicker({
                language: 'es',
                format: 'yyyy-mm-dd',
                orientation: 'bottom',
                todayHighlight: true,
                autoclose: true,
            });

            //Select2 Configuration for customers
            $('.select2').select2();
        })
    </script>
@endpush

