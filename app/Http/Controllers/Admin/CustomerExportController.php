<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Exporter;
use App\Http\Controllers\Controller;
use App\Exports\CustomersExcelExport;

class CustomerExportController extends Controller
{
    /**
     * @var Excel
     */
    private $excel;

    public function __construct(Exporter  $excel)
    {
        $this->excel = $excel;
    }

    public function excel()
    {
        return $this->excel->download(new CustomersExcelExport, 'customers.xlsx');
    }
}
