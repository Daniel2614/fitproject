@extends('layouts.app')

@section('contentheader_title')
    Niveles
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 xl:tw-flex xl:tw-justify-start">
            <div class=" lg:tw-flex lg:tw-items-center xl:tw-w-2/5">
                <h3 class="tw-pr-4 tw-mb-2">Crear Nuevo:</h3>
                <a class="vg-button tw-py-2 tw-bg-grey hover:tw-bg-indigo"
                   href="{{ route('admin.levels.create') }}">
                    <i class=" glyphicon glyphicon-file"></i>
                    Nuevo
                </a>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                <thead>
                    <th class="text-center">ID</th>
                    <th class="text-center">Nombre</th>
                    <th class="text-center">Clientes</th>
                    <th class="text-center">Rutinas</th>
                    <th class="text-center">Acciones</th>
                </thead>
                <tbody>
                    @foreach($levels as $level)
                        <tr>
                            <td>{{ $level->id }}</td>
                            <td>{{ $level->name }}</td>
                            <td>{{ $level->customers_count }}</td>
                            <td>{{ $level->routines_count }}</td>
                            <td class="tw-flex tw-justify-center tw-items-center">
                                <a href="{{ route('admin.levels.show', $level) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('admin.levels.edit', $level) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.levels.destroy', $level) }}"  method="post" class="form-delete">
                                    @csrf
                                    @method('delete')
                                    <button class="tw-px-2 tw-text-2xl tw-text-grey hover:tw-text-red-light js-button-delete focus:tw-outline-none">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection
