@extends('layouts.auth')

@section('htmlheader_title')
    Password reset
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
            <p class="tw-text-base">{{ trans('adminlte_lang::message.passwordreset') }}</p>
            <form class="form" action="{{ url('/password/reset') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password" name="password"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password" name="password_confirmation"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="tw-text-center">
                    <button type="submit" class="btn btn-primary  tw-mb-4">{{ trans('adminlte_lang::message.passwordreset') }}</button>
                </div>
            </form>
            <a class="tw-font-bold tw-text-base" href="{{ url('/login') }}">Iniciar sesión</a><br>
        </div><!-- /.vg-login__content -->
    </div><!-- /.vg-login -->
@endsection
