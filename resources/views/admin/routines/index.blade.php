@extends('layouts.app')

@section('contentheader_title')
    Rutinas
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3">
            <div class=" lg:tw-flex lg:tw-items-center xl:tw-w-2/5">
                <h3 class="tw-pr-4 tw-mb-2">Crear Nueva:</h3>
                <a class="vg-button tw-py-2 tw-bg-grey hover:tw-bg-indigo"
                   href="{{ route('admin.routines.create') }}">
                    <i class=" glyphicon glyphicon-file"></i>
                    Nueva
                </a>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                <thead>
                <th class="text-center">Nombre</th>
                <th class="text-center">Nivel</th>
                <th class="text-center">N# Clientes</th>
                <th class="text-center">Acciones</th>
                </thead>
                <tbody>
                @foreach($routines as $routine)
                    <tr>
                        <td class="tw-capitalize">{{ $routine->name }}</td>
                        <td>{{ $routine->level->name }}</td>
                        <td>{{ $routine->customers_count }}</td>
                        <td class="tw-flex tw-justify-center tw-items-center">
                            <a href="{{ route('admin.routines.show', $routine) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-eye"></i></a>
                            <a href="{{ route('admin.routines.edit', $routine) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-edit"></i></a>
                            <form action="{{ route('admin.routines.destroy', $routine) }}"  method="post" class="form-delete">
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
            <div class="text-center">
                {{ $routines->links() }}
            </div>
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection
