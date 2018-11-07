<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/3
 * Time: 下午 05:14
 */

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    public function export()
    {
        Excel::create('Coupon', function($excel) {

            $excel->sheet('SN', function($sheet) {

                // This logic get the columns that need to be exported from the table data
                $rows = collect($this->getData())->map(function ($item) {
                    return array_only($item, ['sn', 'status']);
                });

                $sheet->rows($rows);

            });

        })->export('xls');
    }
}