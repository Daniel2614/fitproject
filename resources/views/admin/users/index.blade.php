@extends('layouts.app')

@section('contentheader_title')
    Usuarios
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 lg:tw-flex lg:tw-items-center">
            <h3 class="tw-pr-4 tw-mb-2">Crear Nuevo:</h3>
            <a class="vg-button tw-py-2 tw-bg-grey hover:tw-bg-indigo"
               href="{{ route('admin.users.create') }}">
                <i class=" glyphicon glyphicon-file"></i>
                Nuevo
            </a>
        </div><!-- /.box-header -->
        <div class="box-body tw-px-0 tw-text-center">
            <table class="table table-striped table-hover tw-min-w-lg tw-text-center">
                <thead>
                    <th class="text-center">id</th>
                    <th class="text-left">Nombres</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Creado</th>
                    <th class="text-center">Accciones</th>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="tw-text-left">
                                <img class="tw-h-10 tw-w-10 tw-rounded-full" src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                                <span class="tw-pl-3">{{ $user->full_name }}</span>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if( $user->active )
                                    <span class="tw-bg-green tw-text-white tw-p-1 tw-rounded tw-text-xs">
                                       {{  $user->status }}
                                    </span>
                                @else
                                    <span class="tw-bg-red tw-text-white tw-p-1 tw-rounded tw-text-xs">
                                        {{  $user->status }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td class="tw-flex tw-justify-center tw-items-center">
                                <a href="{{ route('admin.users.edit', $user) }}" class="tw-px-2 tw-text-2xl tw-text-indigo"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.users.destroy', $user) }}"  method="post" class="form-delete">
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
            <div>
                {{ $users->links() }}
            </div>
        </div><!-- /.end box-body -->
    </div><!-- /.end box -->
@endsection

