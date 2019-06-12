@extends('layouts.app')

@section('contentheader_title')
    Detalles Cliente - {{ $customer->full_name }}
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Detalles de cliente</h2>
        </div>
        <div class="box-body">
            <div class="tw-shadow tw-rounded-lg tw-overflow-hidden">
                <h2 class="tw-bg-grey-light tw-px-4 tw-text-black tw-py-3 tw-text-lg tw-text-center md:tw-text-left">Cliente</h2>
                <div class="tw-px-6 tw-py-6 lg:tw-flex lg:tw-items-center tw-text-center md:tw-text-left">
                    <div class="tw-rounded-full tw-overflow-hidden tw-w-32 tw-inline-block tw-mr-8 tw-border-4 tw-border-grey-light">
                        <img src="{{ Storage::url($customer->avatar) }}" alt="Avatar">
                    </div>
                    <div>
                        <h3 class="tw-mb-px">
                            {{ $customer->full_name }}
                        </h3>
                        <a class="tw-inline-block tw-mb-3">
                            <i class="fa fa-envelope tw-mr-1"></i>
                            {{ $customer->email }}
                        </a>
                        <p class="tw-mb-1">
                            <i class="fa fa-phone tw-mr-2"></i>
                            {{ $customer->phone }} / {{ $customer->cell_phone }}
                        </p>
                        <p>
                            <i class="fa fa-address-book tw-mr-1"></i>
                            {{ $customer->address }}
                        </p>
                    </div>
                </div><!-- ./End tw-flex-->
                <table class="table table-striped tw-table-auto tw-mb-0">
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Cédula:</td>
                        <td class="tw-uppercase tw-text-xs">{{ $customer->ci ?? 'Vacio' }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Género:</td>
                        <td class="tw-uppercase tw-text-xs">{{ $customer->gender }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Fecha Nacimiento</td>
                        <td class="tw-uppercase tw-text-xs">{{ $customer->birthdate->toDateString() }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Estado:</td>
                        @if( $customer->user->active )
                            <td class="tw-text-xs">
                                <span class="tw-inline-block tw-bg-green tw-text-white tw-py-px tw-px-2 tw-rounded">activo</span>
                            </td>
                        @else
                            <td class="tw-text-xs">
                                <span class="tw-inline-block tw-bg-red tw-text-white tw-py-px tw-px-2 tw-rounded">inactivo</span>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Nivel:</td>
                        <td class="tw-uppercase tw-text-xs">{{ $customer->level->name }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Rutina:</td>
                        <td class="tw-uppercase tw-text-xs">
                            <a class="tw-text-indigo" href="{{ route('admin.routines.download', $customer->routine) }}">
                                <i class="fa fa-download"></i>
                                {{ $customer->routine->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Observaciones Médicas:</td>
                        <td class="tw-text-xs">{{ $customer->medical_observations }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Miemrbro desde:</td>
                        <td class="tw-uppercase tw-text-xs">{{ $customer->created_at->diffForHumans() }}</td>
                    </tr>
                </table>
            </div><!-- ./End tw-shadow-->
            <div class="tw-mt-6">
                <a href="{{ route('admin.customers.edit', $customer) }}"
                   class="vg-button tw-text-white tw-bg-indigo tw-inline-flex tw-items-center tw-border-indigo tw-mr-1">
                    <i class="fa fa-pencil tw-mr-1 tw-text-base"></i>
                    Editar
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                    <i class="fa fa-undo tw-text-base tw-mr-1"></i>
                    Volver
                </a>
            </div><!-- ./End tw-m-b-->
        </div><!-- ./End box body-->
    </div><!-- ./End box default-->
@endsection

