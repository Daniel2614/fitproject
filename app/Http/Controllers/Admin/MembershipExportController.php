<?php

namespace App\Http\Controllers\Admin;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Exporter;
use App\Http\Controllers\Controller;
use App\Exports\MembershipsExcelExport;

class MembershipExportController extends Controller
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
        return $this->excel->download(new MembershipsExcelExport, 'memberships.xlsx');
    }
}
