@extends('layouts.app')

@section('contentheader_title')
    Planes
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 lg:tw-flex lg:tw-items-center">
            <h3 class="tw-pr-4 tw-mb-2">Administrat planes:</h3>
            <a class="vg-button tw-py-2 tw-bg-grey hover:tw-bg-indigo"
               href="{{ route('admin.plans.create') }}">
                <i class=" glyphicon glyphicon-file"></i>
                Nuevo
            </a>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <div class="lg:tw-flex tw-flex-wrap">
                <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                    <thead>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Precio</th>
                        <th class="text-center">Premium</th>
                        <th class="text-center"># Membresías</th>
                        <th class="text-center">Creado</th>
                        <th class="text-center">Acciones</th>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr>
                                <td class="tw-capitalize">{{ $plan->name }}</td>
                                <td>${{ $plan->price_in_dollars }}</td>
                                <td>{{ $plan->is_premium ? 'si' : 'no' }}</td>
                                <td>{{ $plan->memberships_count }}</td>
                                <td>{{ $plan->created_at->format('d-m-Y') }}</td>
                                <td class="tw-flex tw-justify-center tw-items-center">
                                    <a href="{{ route('admin.plans.edit', $plan) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-edit"></i></a>
                                    <form action="{{ route('admin.plans.destroy', $plan) }}"  method="post" class="form-delete">
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
            {{ $plans->links() }}
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection

