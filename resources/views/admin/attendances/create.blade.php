@extends('layouts.app')

@section('contentheader_title')
    Nueva asistencia
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Registro Asistencia</h2>
        </div>
        <div class="box-body">
            <form action="{{ route('admin.attendances.store') }}" method="post" autocomplete="off">
                @csrf
                <div class="form-group {{  $errors->has('customer_id') ? 'has-error': '' }}">
                    <label>Seleccionar cliente</label>
                    <select name="customer_id" class="form-control select2" tabindex="-1">
                        @foreach($customers as $customer)
                            <option  value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }} data-avatar="{{ Storage::url($customer->avatar) }}">
                                {{ $customer->full_name }} - {{ $customer->email }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('customer_id'))
                        <span class="help-block">{{ $errors->first('customer_id') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('date') ? ' has-error': '' }}">
                    <label>Fecha:</label>
                    <input class="form-control flatpicker" type="text" name="date" value="{{ old('date') }}">
                    @if ($errors->has('date'))
                        <span class="help-block">{{ $errors->first('date') }}</span>
                    @endif
                </div>

                <div class="tw-mt-6">
                    <button type="submit" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center">
                        <i class="fa fa-save tw-mr-1 tw-text-base"></i>
                        Guardar
                    </button>
                    <a href="{{ route('admin.attendances.index') }}"
                       class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                        <i class="fa fa-undo tw-text-base tw-mr-1"></i>
                        Volver
                    </a>
                </div><!-- ./End tw-m-b-->
            </form>
        </div>
    </div><!-- ./End box default-->
@endsection

@push('footer-scripts')
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script>
        $(document).ready(function () {
            flatpickr(".flatpicker", {
                locale: "es",
                enableTime: true,
                dateFormat: "Y-m-d H:i:S",
                altInput: true,
                altFormat: "l j F, Y H:i:S",
                defaultDate: new Date(),
                maxDate: new Date(),
                time_24hr: true,
            });

            //Select2 configuration
            function formatState (state) {
                if (!state.id) { return state.text; }
                let $state = $(
                    '<span>' +
                        '<img class="tw-rounded-full tw-w-8 tw-h-8 tw-mr-2" src="'+state.element.dataset.avatar+'"/> ' +
                        state.text +
                    '</span>'
                );
                return $state;
            }

            $('.select2').select2({
                placeholder: 'Seleccionar  un cliente',
                allowClear: true,
                templateResult: formatState,
            });
        })
    </script>
@endpush
