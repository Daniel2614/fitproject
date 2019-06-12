<?php

namespace App\Exports;

use App\VitalGym\Entities\Membership;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class MembershipsExcelExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    /**
     * @param mixed $membership
     *
     * @return array
     */
    public function map($membership): array
    {
        return [
            $membership->id,
            $membership->customer->full_name,
            $membership->customer->email,
            $membership->plan->name,
            $membership->plan->is_premium ? 'Si' : 'No',
            $membership->payment->membership_quantity,
            $membership->plan->price_in_dollars,
            $membership->payment->total_price_in_dollars,
            $membership->date_start->toDateString(),
            $membership->date_end->toDateString(),
            $membership->created_at,
        ];
    }

    public function query()
    {
        return Membership::query()->with('customer', 'plan', 'payment')->orderByDesc('created_at');
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Cliente',
            'Email',
            'Plan',
            'Premium',
            'Cantidad',
            'Precio Unitario',
            'Precio Total',
            'Fecha Inicio',
            'Fecha Expiración',
            'Fecha Creación',
        ];
    }
}
