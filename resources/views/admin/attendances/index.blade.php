@extends('layouts.app')

@section('contentheader_title')
    Asistencias
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 lg:tw-flex lg:tw-items-center">
            <h3 class="tw-pr-4 tw-mb-2">Registro asistencias:</h3>
            <a class="vg-button tw-py-2 tw-bg-grey hover:tw-bg-indigo"
               href="{{ route('admin.attendances.create') }}">
                <i class=" glyphicon glyphicon-file"></i>
                Nuevo
            </a>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <div class="lg:tw-flex tw-flex-wrap">
                <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                    <thead>
                    <th class="text-center">ID</th>
                    <th class="text-left">Cliente</th>
                    <th class="text-center">Fecha Asistencia</th>
                    <th class="text-center">Fecha Creación</th>
                    <th class="text-center">Acciones</th>
                    </thead>
                    <tbody>
                    @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->id }}</td>
                            <td class="tw-text-left">
                                <img class="tw-w-10 tw-h-10 tw-rounded-full tw-mr-2" src="{{ Storage::url($attendance->customer->avatar ) }}" alt="{{ $attendance->customer->full_name }}">
                                {{ $attendance->customer->full_name }} - {{ $attendance->customer->email }}
                            </td>
                            <td>{{ $attendance->date }}</td>
                            <td>{{ $attendance->created_at }}</td>
                            <td class="tw-flex tw-justify-center tw-items-center">
                                <form action="{{ route('admin.attendances.destroy', $attendance) }}"  method="post" class="form-delete">
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
            </div><!-- /.end tw-flex -->
            {{ $attendances->links() }}
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection

