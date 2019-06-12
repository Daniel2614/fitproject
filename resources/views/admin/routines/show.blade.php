@extends('layouts.app')

@section('contentheader_title')
    Detalles Rutina - N# {{ $routine->id }}
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Detalles de la rutina</h2>
        </div>
        <div class="box-body p">
            <div class="tw-shadow tw-rounded-lg tw-overflow-hidden">
                <h2 class="tw-bg-grey-light tw-px-4 tw-text-black tw-py-3 tw-text-lg tw-text-center md:tw-text-left">Rutina</h2>
                <div class="tw-px-6 tw-py-6 lg:tw-flex lg:tw-items-center tw-text-center md:tw-text-left">
                    <div>
                        <h3 class="tw-mb-px">{{ $routine->name }}</h3>
                    </div>
                </div><!-- ./End tw-flex-->
                <table class="table table-striped tw-table-auto tw-mb-0">
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Nivel:</td>
                        <td class="tw-capitalize tw-text-xs">{{ $routine->level->name }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Descripción:</td>
                        <td>{{ $routine->description ?? 'Ninguna' }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Archivo:</td>
                        <td>
                            <a href="{{ route('admin.routines.download', $routine) }}" class="tw-px-2 tw-text-2xl tw-text-indigo">
                                <i class="fa fa-download"></i>
                            </a>
                        </td>
                    </tr>
                </table>
            </div><!-- ./End tw-shadow-->
            <div class="tw-mt-6">
                <a href="{{ route('admin.routines.edit', $routine) }}"
                   class="vg-button tw-text-white tw-bg-indigo tw-inline-flex tw-items-center tw-border-indigo tw-mr-1">
                    <i class="fa fa-pencil tw-mr-1 tw-text-base"></i>
                    Editar
                </a>
                <a href="{{ route('admin.routines.index') }}"
                   class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                    <i class="fa fa-undo tw-text-base tw-mr-1"></i>
                    Volver
                </a>
            </div><!-- ./End tw-m-b-->
        </div><!-- ./End box body-->
    </div><!-- ./End box default-->
@endsection

