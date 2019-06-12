@extends('layouts.auth')

@section('htmlheader_title')
    Log in
@endsection

@section('content')
    <div class="vg-login">
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
        @include('layouts.partials.alerts')
        <div class="vg-login__content">
            <p class="tw-text-base tw-mb-3"> {{ trans('adminlte_lang::message.siginsession') }} </p>
            <form action="{{ url('/login') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="{{ trans('adminlte_lang::message.email') }}" name="email" value="{{ old('email') }}"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="{{ trans('adminlte_lang::message.password') }}" name="password"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <label class="tw-mb-6">
                    <input type="checkbox" name="remember">
                    <span class="tw-text-grey-darker">Recuérdame</span>
                </label>
                <button type="submit" class="btn btn-primary tw-block tw-w-full tw-mb-4">
                    <i class="fa fa-sign-in tw-mr-1" aria-hidden="true"></i>Entrar
                </button>
            </form>
            <a class="tw-font-bold tw-text-base" href="{{ url('/password/reset') }}">{{ trans('adminlte_lang::message.forgotpassword') }}</a>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
@endsection
