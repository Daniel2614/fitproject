@extends('layouts.app')

@section('contentheader_title')
    Editar Usuario
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Llene el siguiente formulario</h2>
        </div>
        <div class="box-body">
            <form class="tw-rounded-lg tw-shadow tw-bg-grey-lightest tw-px-10 tw-py-6" action="{{ route('admin.users.update', $user) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <h3 class="tw-mb-4 tw-mt-6 tw-text-xl tw-border-b-2 tw-border-indigo tw-pb-1 tw-text-indigo">
                    Datos personales
                </h3>
                <div class="form-group {{ $errors->has('avatar') ? ' has-error': '' }}">
                    <img class="tw-w-48 tw-h-48 tw-rounded-full tw-mb-4" src="{{ Storage::url($user->avatar) }}" alt="Avatar">
                    <input type="file" name="avatar">
                    @if ($errors->has('avatar'))
                        <span class="help-block">{{ $errors->first('avatar') }}</span>
                    @endif
                </div>

                <div class="xl:tw-flex xl:tw-flex-wrap">
                    <div class="form-group xl:tw-w-1/2 xl:tw-pr-2 {{ $errors->has('name') ? ' has-error': '' }}">
                        <label>Nombre:</label>
                        <input class="form-control" type="text" name="name" value="{{ old('name', $user->name) }}">
                        @if ($errors->has('name'))
                            <span class="help-block">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pl-2 {{ $errors->has('last_name') ? ' has-error': '' }}">
                        <label>Apellido:</label>
                        <input class="form-control" type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                        @if ($errors->has('last_name'))
                            <span class="help-block">{{ $errors->first('last_name') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pr-2 {{ $errors->has('email') ? ' has-error': '' }}">
                        <label>Email:</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}">
                        @if ($errors->has('email'))
                            <span class="help-block">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pl-2 {{ $errors->has('address') ? 'has-error': '' }}">
                        <label>Dirección:</label>
                        <input class="form-control" type="text" name="address" value="{{ old('address', $user->address) }}">
                        @if ($errors->has('address'))
                            <span class="help-block">{{ $errors->first('address') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pr-2 {{ $errors->has('phone') ? ' has-error': '' }}">
                        <label>Teléfono:</label>
                        <input class="form-control" type="tel" name="phone" value="{{ old('phone', $user->phone) }}">
                        @if ($errors->has('phone'))
                            <span class="help-block">{{ $errors->first('phone') }}</span>
                        @endif
                    </div>
                    <div class="form-group xl:tw-w-1/2 xl:tw-pl-2 {{ $errors->has('cell_phone') ? ' has-error': '' }}">
                        <label>Celular:</label>
                        <input class="form-control" type="tel" name="cell_phone" value="{{ old('cell_phone', $user->cell_phone) }}">
                        @if ($errors->has('cell_phone'))
                            <span class="help-block">{{ $errors->first('cell_phone') }}</span>
                        @endif
                    </div>
                </div><!-- ./End Flex--->


                <h3 class="tw-mb-4 tw-mt-6 tw-text-xl tw-border-b-2 tw-border-indigo tw-pb-1 tw-text-indigo">
                    Datos de acceso
                </h3>

                <div class="form-group  {{ $errors->has('active') ? 'has-error': '' }}">
                    <label>Estado:</label>
                    <select name="active" class="form-control">
                        <option disabled selected>Seleccionar estado</option>
                        <option value="1" {{ old('active', $user->active) == true ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('active', $user->active) == false ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @if ($errors->has('gender'))
                        <span class="help-block">{{ $errors->first('active') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('password') ? ' has-error': '' }}">
                    <label>Contreseña:</label>
                    <input class="form-control" type="password" name="password">
                    @if ($errors->has('password'))
                        <span class="help-block">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('password') ? ' has-error': '' }}">
                    <label>Repetir Contraseña:</label>
                    <input class="form-control" type="password" name="password_confirmation">
                </div>

                <button type="submit" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center tw-border-indigo tw-mr-1">
                    <i class="fa fa-save tw-mr-1 tw-text-base"></i>
                    Actualizar
                </button>
                <a href="/"
                   class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                    <i class="fa fa-undo tw-mr-1 tw-text-base"></i>
                    Volver
                </a>
            </form><!-- ./Endform -->
        </div><!-- ./End box body -->
    </div><!-- ./End box default -->
@endsection