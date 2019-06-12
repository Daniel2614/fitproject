@extends('layouts.app')

@section('contentheader_title')
    Historial de mis Membresías
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 xl:tw-flex xl:tw-justify-start">
            <div class=" lg:tw-flex lg:tw-items-center xl:tw-w-2/5">
                <h3 class="tw-pr-4 tw-mb-2 tw-text-lg">Detalles:</h3>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                <thead>
                <th class="text-left">Cliente</th>
                <th class="text-left">Email</th>
                <th class="text-center">Tipo</th>
                <th class="text-center">Inicio</th>
                <th class="text-center">Expira</th>
                </thead>
                <tbody>
                @foreach($memberships as $membership)
                    <tr>
                        <td class="tw-text-left">
                            <img class="tw-w-10 tw-h-10 tw-rounded-full tw-mr-2" src="{{ Storage::url( $membership->customer->avatar ) }}" alt="{{ $membership->customer->full_name }}">
                            {{ $membership->customer->full_name }}
                        </td>
                        <td class="tw-text-left">{{ $membership->customer->email }}</td>
                        <td class="tw-capitalize">{{ $membership->plan->name }}</td>
                        <td>{{ $membership->date_start->format('Y-m-d') }}</td>
                        <td>
                            @if( $membership->date_end < now())
                                <span class="tw-bg-red tw-text-white tw-text-xs tw-px-3 tw-py-1 tw-rounded tw-inline-block">
                                        {{ $membership->date_end->format('Y-m-d') }}
                                    </span>
                            @else
                                <span class="tw-bg-green tw-text-white tw-text-xs tw-px-3 tw-py-1 tw-rounded tw-inline-block">
                                        {{ $membership->date_end->format('Y-m-d') }}
                                    </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-center">
                {{ $memberships->links() }}
            </div>
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection
