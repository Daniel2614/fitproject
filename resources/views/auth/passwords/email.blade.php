@extends('layouts.auth')

@section('htmlheader_title')
    Password recovery
@endsection

@section('content')
    <div class="vg-login">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> {{ trans('adminlte_lang::message.someproblems') }}<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="vg-login__content">
            <p class="tw-text-base tw-text-center">Restablecer contraseña</p>
            <form action="{{ url('/password/email') }}" method="post" class="tw-mb-3">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="tw-text-center">
                    <button type="submit" class="btn btn-primary tw-mb-4">Restablecer Contraseña</button>
                </div>
            </form>
            <a class="tw-font-bold tw-text-base" href="{{ url('/login') }}">Iniciar sesión</a><br>
        </div><!-- /.vg-login-content -->
    </div><!-- /.vg-login-->
@endsection
