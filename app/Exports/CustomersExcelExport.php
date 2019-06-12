<?php

namespace App\Exports;

use App\VitalGym\Entities\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomersExcelExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize
{
    /**
     * @param \App\VitalGym\Entities\Customer $customer
     *
     * @return array
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->full_name,
            $customer->email,
            $customer->ci,
            $customer->birthdate->toDateString(),
            $customer->gender,
            $customer->medical_observations,
            $customer->routine->name,
            $customer->level->name,
            $customer->created_at,
        ];
    }

    public function query()
    {
        return Customer::query()->with('level', 'routine')->orderByDesc('created_at');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombres',
            'Email',
            'Cédula',
            'Fecha Nacimiento',
            'Género',
            'Observaciones Medicas',
            'Rutina',
            'Nivel',
            'Fecha Creacióm',
        ];
    }
}
