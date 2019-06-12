@extends('layouts.app')

@section('contentheader_title')
    Asistencias
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 xl:tw-flex xl:tw-justify-start">
            <div class=" lg:tw-flex lg:tw-items-center xl:tw-w-2/5">
                <h3 class="tw-pr-4 tw-mb-2 tw-text-lg">Mis asistencias:</h3>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <div class="lg:tw-flex tw-flex-wrap">
                <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                    <thead>
                    <th class="text-center"></th>
                    <th class="text-center">Fecha Asistencia</th>
                    <th class="text-center">Fecha Creación</th>
                    </thead>
                    <tbody>
                    @foreach($attendances as $attendance)
                        <tr>
                            <td class="tw-text-left">
                                <img class="tw-w-10 tw-h-10 tw-rounded-full tw-mr-2" src="{{ Storage::url($attendance->customer->avatar ) }}" alt="{{ $attendance->customer->full_name }}">
                                {{ $attendance->customer->full_name }} - {{ $attendance->customer->email }}
                            </td>
                            <td>{{ $attendance->date->format('Y-m-d H:i') }}</td>
                            <td>{{ $attendance->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.end tw-flex -->
            {{ $attendances->links() }}
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection

