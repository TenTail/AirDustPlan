<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use Excel;

class DataExportController extends Controller
{
    public function index()
    {
        return view("excel_export.index");
    }
    
    public function export(Request $request)
    {
        $time = $request->input("year")."-".sprintf("%02d", $request->input("month"))."%";
        $output_data = $request->input("output_data");
        $country = $request->input("county");
        $output = "`sitename`,";
        $row1 = array();
        array_push($row1, '測站');
        for ($i=0, $size = count($output_data); $i < $size; $i++) { 
            $output = $output."`".$output_data[$i]."`,";
            array_push($row1, $output_data[$i]);
        }
        $output = $output."`publish_time`";
        array_push($row1, '時間');

        $result = DB::select("SELECT $output 
                              FROM `airpollutions` 
                              WHERE `publish_time` LIKE '$time' AND `country` = '$country'");

        Excel::create($time.'-'.$country, function($excel) use($row1, $result) {
            $excel->sheet('Sheet 1', function($sheet) use($row1, $result) {
                $sheet->row(1, $row1);
                $i = 2;
                foreach ($result as $key => $value) {
                    $excel_data = array();
                    foreach ($value as $key => $data) {
                        array_push($excel_data, $data);
                    }
                    $sheet->row($i++, $excel_data);
                }
            });
        })->download('xls');

        return redirect()->route('excel-export.index');
    }
}
