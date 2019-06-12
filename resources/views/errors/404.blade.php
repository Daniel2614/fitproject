@extends('layouts.auth')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.pagenotfound') }}
@endsection

@section('contentheader_title')
    {{ trans('adminlte_lang::message.404error') }}
@endsection

@section('contentheader_description')
@endsection

@section('content')
    <div class="tw-text-center">
        <h2 class="tw-mb-0 text-yellow tw-text-6xl tw-font-bold">404</h2>
        <h3 class="tw-mt-0">
            <i class="fa fa-warning text-yellow"></i>
            Oops! Página no encontrada.
        </h3>
        <p class="tw-text-lg tw-leading-loose">
            No hemos podido encontrar la página que estabas buscando
        </p>
        <a class="btn btn-primary tw-uppercase" href='{{ url('/') }}'>
            Volver
        </a>
    </div>
@endsection
