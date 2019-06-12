@extends('layouts.app')

@section('contentheader_title')
   Nuevo plan
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Crear plan</h2>
        </div>
        <div class="box-body">
            <form action="{{ route('admin.plans.store') }}" method="post" autocomplete="off">
                @csrf
                <div class="form-group {{ $errors->has('name') ? ' has-error': '' }}">
                    <label>Nombre:</label>
                    <input class="form-control" type="text" name="name" value="{{ old('name') }}">
                    @if ($errors->has('name'))
                        <span class="help-block">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('price') ? ' has-error': '' }}">
                    <label>Precio:</label>
                    <div class="tw-flex">
                        <div class="tw-px-3 tw-py-2 tw-leading-none tw-border-r-0 tw-border {{ $errors->has('price') ? ' tw-border-red': '' }}">
                            <i class="fa fa-dollar"></i>
                        </div>
                        <input type="text"
                               name="price"
                               class="form-control tw-rounded-l-none"
                               id="js-input-price"
                               value="{{ old('price') }}">
                    </div>

                    @if ($errors->has('price'))
                        <span class="help-block">{{ $errors->first('price') }}</span>
                    @endif
                </div>

                <div class="form-group {{ $errors->has('is_premium') ? ' has-error': '' }}">
                    <label>Premium:</label>
                    <select name="is_premium" class="form-control">
                        <option value="">Seleccionar tipo</option>
                        <option value="1" {{ old('is_premium') == "1" ? 'selected' : '' }}>Si</option>
                        <option value="0" {{ old('is_premium') == "0" ? 'selected' : '' }}>No</option>
                    </select>
                    @if ($errors->has('is_premium'))
                        <span class="help-block">{{ $errors->first('is_premium') }}</span>
                    @endif
                </div>


                <div class="tw-mt-6">
                    <button type="submit" class="vg-button tw-bg-indigo tw-inline-flex tw-items-center">
                        <i class="fa fa-save tw-mr-1 tw-text-base"></i>
                        Actualizar
                    </button>
                    <a href="{{ route('admin.plans.index') }}"
                       class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                        <i class="fa fa-undo tw-text-base tw-mr-1"></i>
                        Volver
                    </a>
                </div><!-- ./End tw-m-b-->
            </form>
        </div>
    </div><!-- ./End box default-->
@endsection