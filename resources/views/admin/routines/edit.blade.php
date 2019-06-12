@extends('layouts.app')

@section('contentheader_title')
    Editar Rutina
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Detalles de rutina</h2>
        </div>
        <div class="box-body">
            <form action="{{ route('admin.routines.update', $routine) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('patch')
                <div class="form-group {{ $errors->has('name') ? ' has-error': '' }}">
                    <label>Nombre</label>
                    <input name="name" type="text" class="form-control" value="{{ old('name', $routine->name) }}">
                    @if ($errors->has('name'))
                        <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group {{  $errors->has('level_id') ? 'has-error': '' }}">
                    <label>Nivel</label>
                    <select name="level_id" class="form-control select2" tabindex="-1">
                        <option>Selecciona un nivel:</option>
                        @foreach($levels as $level)
                            <option  value="{{ $level->id }}" {{ old('level_id', $routine->level_id) == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                    @if ($errors->has('level_id'))
                        <span class="help-block">{{ $errors->first('level_id') }}</span>
                    @endif
                </div>
                <div class="form-group lg:tw-w-1/2 {{ $errors->has('file') ? ' has-error': '' }}">
                    <label class="tw-block tw-w-full">Archivo actual:</label>
                    <a class="vg-button tw-bg-blue tw-py-2 tw-px-2 tw-text-white tw-mb-2" href="{{ route('admin.routines.download', $routine) }}">
                        <i class="fa fa-download"></i>
                        Descargar
                    </a>
                    <input name="file" type="file" value="{{ old('file') }}">
                    @if ($errors->has('file'))
                        <span class="help-block">{{ $errors->first('file') }}</span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('description') ? ' has-error': '' }}">
                    <label>Descripción:</label>
                    <textarea name="description" class="form-control" cols="20" rows="5">{{ old('description', $routine->description)  }}</textarea>
                    @if ($errors->has('description'))
                        <span class="help-block">{{ $errors->first('description') }}</span>
                    @endif
                </div>
                <div class="tw-mt-6">
                    <button type="submit"
                            class="vg-button tw-text-white tw-bg-indigo tw-inline-flex tw-items-center tw-border-indigo tw-mr-1">
                        <i class="fa fa-pencil tw-mr-1 tw-text-base"></i>
                        Actualizar
                    </button>
                    <a href="{{ route('admin.routines.index') }}"
                       class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                        <i class="fa fa-undo tw-text-base tw-mr-1"></i>
                        Volver
                    </a>
                </div><!-- ./End tw-m-b-->
            </form>
        </div>
    </div><!-- ./End box default-->
@endsection
