@extends('layouts.app')

@section('contentheader_title')
    Clientes
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 xl:tw-flex xl:tw-justify-between">
            <div class=" lg:tw-flex lg:tw-items-center xl:tw-w-2/5">
                <h3 class="tw-pr-4 tw-mb-2">Crear Nuevo:</h3>
                <a class="vg-button tw-py-2 tw-bg-grey hover:tw-bg-indigo"
                   href="{{ route('admin.customers.create') }}">
                    <i class=" glyphicon glyphicon-file"></i>
                    Nuevo
                </a>
            </div>
            <div class="tw-pt-6 xl:tw-pt-0 xl:tw-w-3/5">
                <form action="{{ route('admin.customers.index') }}" method="get" class="xl:tw-flex xl:tw-justify-end">
                    <label class="tw-pr-4 tw-mb-0 tw-flex tw-items-center tw-mb-2 xl:tw-mb-0">Buscar por: </label>
                    <div class="form-group tw-mb-2 xl:tw-mb-0">
                        <select class="form-control xl:tw-rounded-r-none xl:tw-border-r-0 tw-h-auto tw-py-2 tw-text-sm" id="js-select-filter">
                            <option selected disabled>seleccionar Filtro</option>
                            <option value="ci">Cédula</option>
                            <option value="email">Email</option>
                        </select>
                    </div>
                    <div class="form-group  tw-mb-2 xl:tw-mb-0">
                        <input type="search"
                               class="form-control lg:tw-rounded-none tw-h-auto tw-py-2 tw-text-sm"
                               placeholder="ci, email" id="js-input-filter">
                    </div>
                    <button class="vg-button tw-bg-indigo hover:tw-bg-indigo lg:tw-rounded-l-none tw-text-xs">
                        <i class="fa fa-search"></i>
                        Buscar
                    </button>
                </form><!-- /.end form -->
            </div><!-- /.end xl:tw-w-3/5 -->
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <table class="table table-striped table-hover tw-min-w-lg tw-text-left">
                <thead>
                    <th class="text-left">Id</th>
                    <th class="text-left">Nombres</th>
                    <th class="text-left">Cédula</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Nivel</th>
                    <th class="text-left">Género</th>
                    <th class="text-center">Acciones</th>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>
                                <img class="tw-h-10 tw-w-10 tw-rounded-full" src="{{ Storage::url($customer->avatar) }}" alt="{{ $customer->name }}">
                                <span class="tw-pl-3">{{ $customer->full_name }}</span>
                            </td>
                            <td>{{ $customer->ci ?? 'Vacio'  }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->level->name }}</td>
                            <td>{{ $customer->gender }}</td>
                            <td class="tw-flex tw-justify-center tw-items-center">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.customers.destroy', $customer) }}"  method="post" class="form-delete">
                                    @csrf
                                    @method('delete')
                                    <button class="tw-px-2 tw-text-2xl tw-text-grey hover:tw-text-red-light js-button-delete focus:tw-outline-none">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody><!-- /.end tbody -->
            </table><!-- /.end table -->
            <div class="text-center">
                {{ $customers->links() }}
            </div>
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection
