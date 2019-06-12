@extends('layouts.app')

@section('contentheader_title')
    Editanto Membresía <span class="tw-font">N# {{ $membership->id  }}</span>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/bootstrap-datepicker3.min.css') }}">
@endpush

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Actualizar membresía</h2>
        </div>
        <div class="box-body">
            <form action="{{ route('admin.memberships.update', $membership) }}" method="post" autocomplete="off">
                @csrf
                @method('PATCH')
                <div class="form-group {{  $errors->has('customer_id') ? 'has-error': '' }}">
                    <label>Cliente</label>
                    <select name="customer_id" class="form-control select2" tabindex="-1">
                        @foreach($customers as $c)
                            <option  value="{{ $c->id }}" {{ old('customer_id', $customer->id) == $c->id ? 'selected' : ''}} data-avatar="{{ Storage::url($c->avatar) }}">
                                {{ $c->name }} {{ $c->last_name }} - {{ $c->email }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('customer_id'))
                        <span class="help-block">{{ $errors->first('customer_id') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('membership_quantity') ? ' has-error': '' }}">
                    <label>Cantidad</label>
                    <input class="form-control" type="number" name="membership_quantity" value="{{ old('membership_quantity', $payment->membership_quantity) ?? 1 }}">
                    @if ($errors->has('membership_quantity'))
                        <span class="help-block">{{ $errors->first('membership_quantity') }}</span>
                    @endif
                </div>
                @if( $plan->is_premium)
                    <div class="form-group {{ $errors->has('total_days') ? ' has-error': '' }}">
                        <label>Días Totales</label>
                        <input class="form-control" type="number" name="total_days" value="{{ old('total_days', $membership->total_days) }}">
                        @if ($errors->has('total_days'))
                            <span class="help-block">{{ $errors->first('total_days') }}</span>
                        @endif
                    </div>
                @endif

                <div class="lg:tw-flex tw-mb-4">
                    <div class="form-group date lg:tw-w-1/2 lg:tw-pr-2 {{ $errors->has('date_start') ? ' has-error': '' }}">
                        <label>Fecha de Inicio</label>
                        <input id="start-datepicker" name="date_start" type="text" class="form-control datepicker" value="{{ old('date_start', $membership->date_start->format('Y-m-d')) }}">
                        @if ($errors->has('date_start'))
                            <span class="help-block">{{ $errors->first('date_start') }}</span>
                        @endif
                    </div>

                    <div class="form-group date lg:tw-w-1/2 lg:tw-pl-2 date {{ $errors->has('date_end') ? ' has-error': '' }}">
                        <label>Fecha de Caducidad</label>
                        <input id="end-datepicker" name="date_end" type="text" class="form-control datepicker" value="{{ old('date_end', $membership->date_end->format('Y-m-d')) }}">
                        @if ($errors->has('date_end'))
                            <span class="help-block">{{ $errors->first('date_end') }}</span>
                        @endif
                    </div>
                </div>

                <div class="tw-mt-6">
                    <button type="submit" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center">
                        <i class="fa fa-save tw-mr-1 tw-text-base"></i>
                        Actualizar
                    </button>
                    <a href="{{ route('admin.memberships.index') }}"
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
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>
    <script>
        $(document).ready( function (e) {
            //Date TimePickers
            let membershipStartDate = "{{ $membership->date_start->toDateString() }}";
            $('#start-datepicker').datepicker({
                language: 'es',
                format: 'yyyy-mm-dd',
                orientation: 'bottom',
                startDate: membershipStartDate,
                todayHighlight: true,
                autoclose: true,
            });

            $('#end-datepicker').datepicker({
                language: 'es',
                format: 'yyyy-mm-dd',
                orientation: 'bottom',
                startDate: membershipStartDate,
                todayHighlight: true,
                autoclose: true,
            });

            //Select2 Configuration for customers
            function formatState (state) {
                if (!state.id) { return state.text; }
                var $state = $(
                    '<span><img class="tw-rounded-full tw-w-8 tw-h-8 tw-mr-2" src="'+state.element.dataset.avatar+'"/> ' + state.text + '</span>'
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

