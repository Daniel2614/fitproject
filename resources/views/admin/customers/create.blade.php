@extends('layouts.app')

@section('contentheader_title')
    Nuveo cliente
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/datepicker/bootstrap-datepicker3.min.css') }}">
@endpush

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Llenar el siguiente formulario para agregar un nuevo cliente</h2>
        </div>
        <div class="box-body">
            <form class="tw-rounded-lg tw-shadow tw-bg-grey-lightest tw-px-10 tw-py-6" action="{{ route('admin.customers.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <h3 class="tw-mb-4 tw-mt-6 tw-text-xl tw-border-b-2 tw-border-indigo tw-pb-1 tw-text-indigo">
                    Datos personales
                </h3>
                <div class="form-group {{ $errors->has('name') ? ' has-error': '' }}">
                    <img class="tw-rounded-full" src="{{ Storage::url('avatars/default-avatar.jpg') }}" alt="Avatar">
                    <input type="file" name="avatar">
                    @if ($errors->has('avatar'))
                        <span class="help-block">{{ $errors->first('avatar') }}</span>
                    @endif
                </div>

                <div class="xl:tw-flex xl:tw-flex-wrap">
                    <div class="form-group xl:tw-w-1/2 xl:tw-pr-2 {{ $errors->has('name') ? ' has-error': '' }}">
                        <label>Nombre:</label>
                        <input class="form-control" type="text" name="name" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <span class="help-block">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pl-2 {{ $errors->has('last_name') ? ' has-error': '' }}">
                        <label>Apellido:</label>
                        <input class="form-control" type="text" name="last_name" value="{{ old('last_name') }}">
                        @if ($errors->has('last_name'))
                            <span class="help-block">{{ $errors->first('last_name') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pr-2 {{ $errors->has('ci') ? ' has-error': '' }}">
                        <label>Cédula (opcional):</label>
                        <input class="form-control" type="number" name="ci" value="{{ old('ci') }}">
                        @if ($errors->has('ci'))
                            <span class="help-block">{{ $errors->first('ci') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pl-2 {{ $errors->has('email') ? ' has-error': '' }}">
                        <label>Email:</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pr-2 {{ $errors->has('phone') ? ' has-error': '' }}">
                        <label>Teléfono:</label>
                        <input class="form-control" type="tel" name="phone" value="{{ old('phone') }}">
                        @if ($errors->has('phone'))
                            <span class="help-block">{{ $errors->first('phone') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pl-2 {{ $errors->has('cell_phone') ? ' has-error': '' }}">
                        <label>Celular:</label>
                        <input class="form-control" type="tel" name="cell_phone" value="{{ old('cell_phone') }}">
                        @if ($errors->has('cell_phone'))
                            <span class="help-block">{{ $errors->first('cell_phone') }}</span>
                        @endif
                    </div>
                </div><!-- ./End Flex--->

                <div class="form-group {{ $errors->has('address') ? 'has-error': '' }}">
                    <label>Dirección:</label>
                    <input class="form-control" type="text" name="address" value="{{ old('address') }}">
                    @if ($errors->has('address'))
                        <span class="help-block">{{ $errors->first('address') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('birthdate') ? 'has-error': '' }}">
                    <label>Fecha Nacimiento</label>
                    <input class="form-control datepicker" type="text" name="birthdate" value="{{ old('birthdate') }}">
                    @if ($errors->has('birthdate'))
                        <span class="help-block">{{ $errors->first('birthdate') }}</span>
                    @endif
                </div>

               <div class="xl:tw-flex">
                   <div class="form-group xl:tw-w-1/3 xl:tw-pr-4 {{ $errors->has('gender') ? 'has-error': '' }}">
                       <label>Género:</label>
                       <select name="gender" class="form-control">
                           <option disabled selected>Seleccionar género</option>
                           <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                           <option value="feminine" {{ old('gender') == 'feminine' ? 'selected' : '' }}>Femenino</option>
                       </select>
                       @if ($errors->has('gender'))
                           <span class="help-block">{{ $errors->first('gender') }}</span>
                       @endif
                   </div>

                   <div class="form-group xl:tw-w-1/3 xl:tw-pr-4 {{ $errors->has('level_id') ? 'has-error': '' }}">
                       <label>Nivel:</label>
                       <select name="level_id" class="form-control">
                           <option disabled selected>Seleccionar nivel</option>
                           @foreach($levels as $level)
                               <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                                   {{ $level->name }}
                               </option>
                           @endforeach
                       </select>
                       @if ($errors->has('level_id'))
                           <span class="help-block">{{ $errors->first('level_id') }}</span>
                       @endif
                   </div>

                   <div class="form-group xl:tw-w-1/3 {{ $errors->has('routine_id') ? 'has-error': '' }}">
                       <label>Rutina:</label>
                       <select name="routine_id" class="form-control">
                           <option disabled selected>Seleccionar Rutina</option>
                           @foreach($routines as $routine)
                               <option value="{{ $routine->id }}" {{ old('routine_id') == $routine->id ? 'selected' : '' }}>
                                   {{ $routine->name }}
                               </option>
                           @endforeach
                       </select>
                       @if ($errors->has('routine_id'))
                           <span class="help-block">{{ $errors->first('routine_id') }}</span>
                       @endif
                   </div>
               </div><!-- End xl:tw-flex-->

                <div class="form-group {{ $errors->has('medical_observations') ? 'has-error': '' }}">
                    <label>Observaciones Médicas (opcional):</label>
                    <textarea class="form-control" type="text" name="medical_observations">{{ old('birthdate') }}</textarea>
                    @if ($errors->has('medical_observations'))
                        <span class="help-block">{{ $errors->first('medical_observations') }}</span>
                    @endif
                </div>

                <h3 class="tw-mb-4 tw-mt-6 tw-text-xl tw-border-b-2 tw-border-indigo tw-pb-1 tw-text-indigo">
                    Datos de acceso
                </h3>

                <div class="form-group {{ $errors->has('password') ? ' has-error': '' }}">
                    <label>Contreseña:</label>
                    <input class="form-control" type="password" name="password">
                    @if ($errors->has('password'))
                        <span class="help-block">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('password') ? ' has-error': '' }}">
                    <label>Repetir Contraseña:</label>
                    <input class="form-control" type="password" name="confirmation_password">
                </div>

                <button type="submit" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center tw-border-indigo tw-mr-1">
                    <i class="fa fa-save tw-mr-1 tw-text-base"></i>
                    Guardar
                </button>
                <a href="{{ route('admin.customers.index') }}"
                   class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                    <i class="fa fa-undo tw-mr-1 tw-text-base"></i>
                    Volver
                </a>
            </form><!-- ./Endform -->
        </div><!-- ./End box body -->
    </div><!-- ./End box default -->
@endsection

@push('footer-scripts')
    <script src="{{ asset('plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('plugins/datepicker/locales/bootstrap-datepicker.es.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.datepicker').datepicker({
                language: 'es',
                format: 'yyyy-mm-dd',
                orientation: 'bottom',
                todayHighlight: true,
                autoclose: true,
            });
        })
    </script>
@endpush

