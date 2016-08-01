<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\Models\AirPollution;
use Excel;

class DataExportController extends Controller
{
    /**
     * Select time (yyyy) 
     *
     * @var Carbon\Carbon
     */
    private $year;

    /**
     * Choose county.
     *
     * @var string
     */
    private $county;

    /**
     * Choose sitename.
     *
     * @var string
     */
    private $sitename;

    /**
     * The column want to output.
     *
     * @var array
     */
    private $output = [];

    /**
     * Column title of Excel.
     *
     * @var array
     */
    private $xls_row = [];

    /**
     * Data of Excel
     *
     * @var array
     */
    private $xls_data = [];

    public function index()
    {
        return view("excel_export.index");
    }
    
    /**
     * Data process.
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $this->year = $request->input("year");
        $this->county = $request->input("county");
        $this->sitename = $request->input("sitename");
        $this->output = $request->input("output_data");

        $result = ($this->county !== NULL) ? $this->getExcelData('county') : $this->getExcelData('sitename');
        
        array_push($this->xls_row, 'sitename');
        if (empty($this->output)) {
            array_push($this->xls_row, 'pm25');
        } else {
            foreach ($this->output as $key => $value) {
                array_push($this->xls_row, $value);
            }
        }
        array_push($this->xls_row, 'publish_time');

        $i = 0;
        foreach ($result as $key => $value) {
            $this->xls_data[$i] = [];
            foreach ($this->xls_row as $k => $v) {
                array_push($this->xls_data[$i], $value[$v]);
            }
            $i++;
        }

        $result = ($this->county !== NULL) ? $this->createExcel($this->county, 'xls') : $this->createExcel($this->sitename, 'xls');
    }

    /**
     * Create and Output Excel.
     *
     * @param string $fname
     * @param string $file_type
     */
    public function createExcel($fname, $file_type)
    {
        $file_name = $fname."-".$this->year;
        $row = $this->xls_row;
        $data = $this->xls_data;

        Excel::create($file_name, function($excel) use($row, $data) {
            $excel->sheet('Sheet 1', function($sheet) use($row, $data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->row(1, $row);
            });
        })->download($file_type);
    }

    /**
     * SQL to get DB data.
     *
     * @param string $col_name
     */
    public function getExcelData($col_name)
    {
        return AirPollution::where($col_name, $this->$col_name)->where('publish_time', 'LIKE', $this->year."%")->get()->toArray();
    }
}
