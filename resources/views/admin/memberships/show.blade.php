@extends('layouts.app')

@section('contentheader_title')
    Detalles Membresía - N# {{ $membership->id }}
@endsection

@section('main-content')
    <div class="box">
        <div class="box-header tw-mb-3 tw-py-0 tw-mb-2">
            <h2 class="tw-text-base">Detalles de la membresía</h2>
        </div>
        <div class="box-body p">
            <div class="tw-shadow tw-rounded-lg tw-overflow-hidden">
                <h2 class="tw-bg-grey-light tw-px-4 tw-text-black tw-py-3 tw-text-lg tw-text-center md:tw-text-left">Cliente</h2>
                <div class="tw-px-6 tw-py-6 lg:tw-flex lg:tw-items-center tw-text-center md:tw-text-left">
                    <div class="tw-rounded-full tw-overflow-hidden tw-w-32 tw-inline-block tw-mr-8 tw-border-4 tw-border-grey-light">
                        <img src="{{ Storage::url($customer->avatar)  }}" alt="Logo">
                    </div>
                    <div>
                        <h3 class="tw-mb-px">{{ $customer->full_name }}</h3>
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
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Plan</td>
                        <td class="tw-uppercase tw-text-xs">{{ $plan->name }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Precio unitario:</td>
                        <td>${{ $plan->price_in_dollars }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Cantidad:</td>
                        <td>{{ $payment->membership_quantity }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Precio Total</td>
                        <td>${{ $payment->total_price_in_dollars }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Premium</td>
                        <td>{{ $plan->is_premium ? 'Si' : 'No' }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Fecha Pago</td>
                        <td>{{ $membership->date_start->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Fecha expiracion</td>
                        <td>{{ $membership->date_end->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td class="tw-font-semibold tw-inline-block tw-ml-6 tw-uppercase tw-text-xs">Creada por:</td>
                        <td>{{ $payment->user->name }} {{ $payment->user->last_name }}</td>
                    </tr>
                </table>
            </div><!-- ./End tw-shadow-->
            
            <div class="tw-mt-6">
                <a href="{{ route('admin.memberships.edit', $membership) }}"
                   class="vg-button tw-text-white tw-bg-indigo tw-inline-flex tw-items-center tw-border-indigo tw-mr-1">
                    <i class="fa fa-pencil tw-mr-1 tw-text-base"></i>
                    Editar
                </a>
                <a href="{{ route('admin.memberships.index') }}"
                   class="vg-button tw-text-black tw-bg-transparent hover:tw-text-black tw-inline-flex tw-items-center tw-border">
                    <i class="fa fa-undo tw-text-base tw-mr-1"></i>
                    Volver
                </a>
            </div><!-- ./End tw-m-b-->
        </div><!-- ./End box body-->
    </div><!-- ./End box default-->
@endsection
