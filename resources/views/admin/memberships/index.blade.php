@extends('layouts.app')

@section('contentheader_title')
    Membresías
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 xl:tw-flex xl:tw-justify-between">
            <div class=" lg:tw-flex lg:tw-items-center xl:tw-w-2/5">
                <h3 class="tw-pr-4 tw-mb-2">Crear Nueva:</h3>
                <a class="vg-button tw-py-2 tw-bg-grey hover:tw-bg-indigo"
                   href="{{ route('plans.index') }}">
                    <i class=" glyphicon glyphicon-file"></i>
                    Nueva
                </a>
            </div>
            <div class="tw-pt-6 xl:tw-pt-0 xl:tw-w-3/5">
                <form action="{{ route('admin.memberships.index') }}" method="get" class="xl:tw-flex xl:tw-justify-end">
                    <label class="tw-pr-4 tw-mb-0 tw-flex tw-items-center tw-mb-2 xl:tw-mb-0">Buscar por: </label>
                    <div class="form-group tw-mb-2 xl:tw-mb-0">
                        <select class="form-control xl:tw-rounded-r-none xl:tw-border-r-0 tw-h-auto tw-py-2 tw-text-sm" id="js-select-filter">
                            <option selected disabled>seleccionar Filtro</option>
                            <option value="name">Nombre</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                    <div class="form-group  tw-mb-2 xl:tw-mb-0">
                        <input type="search"
                               class="form-control lg:tw-rounded-none tw-h-auto tw-py-2 tw-text-sm"
                               placeholder="nombre, email" id="js-input-filter">
                    </div>
                    <button class="vg-button tw-bg-indigo hover:tw-bg-indigo lg:tw-rounded-l-none">
                        <i class="fa fa-search"></i>
                        Buscar
                    </button>
                </form>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                <thead>
                    <th class="text-center">Id</th>
                    <th class="text-left">Cliente</th>
                    <th class="text-left">Email</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-center">Inicio</th>
                    <th class="text-center">Expira</th>
                    <th class="text-center">Acciones</th>
                </thead>
                <tbody>
                    @foreach($memberships as $membership)
                        <tr>
                            <td>
                                {{ $membership->id }}
                            </td>
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
                            <td class="tw-flex tw-justify-center tw-items-center">
                                <a href="{{ route('admin.memberships.show', $membership) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('admin.memberships.edit', $membership) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.memberships.destroy', $membership) }}"  method="post" class="form-delete">
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
                {{ $memberships->links() }}
            </div>
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection
