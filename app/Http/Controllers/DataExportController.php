<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use File;
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
    private $sitename = array(
        '基隆市'=>['基隆'],
        '嘉義市'=>['嘉義'],
        '高雄市'=>['美濃','大寮','橋頭','仁武','鳳山','林園','楠梓','左營','前金','前鎮','小港','復興'],
        '新北市'=>['汐止','萬里','新店','土城','板橋','新莊','菜寮','林口','淡水','三重','永和'],
        '臺北市'=>['士林','中山','萬華','古亭','松山','大同','陽明'],
        '桃園市'=>['桃園','大園','觀音','平鎮','龍潭','中壢'],
        '新竹縣'=>['湖口','竹東'],
        '新竹市'=>['新竹'],
        '苗栗縣'=>['頭份','苗栗','三義'],
        '臺中市'=>['豐原','沙鹿','大里','忠明','西屯'],
        '彰化縣'=>['彰化','線西','二林'],
        '南投縣'=>['南投','竹山','埔里'],
        '雲林縣'=>['斗六','崙背','臺西','麥寮','台西'],
        '嘉義縣'=>['新港','朴子'],
        '臺南市'=>['新營','善化','安南','臺南','台南'],
        '屏東縣'=>['屏東','潮州','恆春'],
        '臺東縣'=>['臺東','關山','台東'],
        '宜蘭縣'=>['宜蘭','冬山'],
        '花蓮縣'=>['花蓮'],
        '澎湖縣'=>['馬公'],
        '連江縣'=>['馬祖'],
        '金門縣'=>['金門'],
    );

    /**
     * The column want to output.
     *
     * @var array
     */
    private $output = "";

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
        $this->year = $request->input("year")-1911;
        $sitename = $request->input("sitename");
        $file_name = $this->year.'年'.$sitename.'站.json';
        $file = public_path().'/history-files/'.$file_name;
        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);
            dd(explode('-', $data[0]["PublishTime"])[1]);
        }

        $fun = function ($value) {
            return "\"$value\"";
        };

        foreach ($this->sitename[$this->county] as $key => $sitename) {
            $file_name = $this->year.'年'.$sitename.'站.json';
            $file = public_path().'/history-files/'.$file_name;
            if (File::exists($file)) {
                $file_content = file_get_contents($file);
                $data = json_decode($file_content, true);
                dd($data);
                 
                $fp = fopen('file.csv', 'w');
                $col = [];
                foreach ($data[0] as $key => $value) {

                    array_push($col, $key);
                }
                fputcsv($fp, $col);
                foreach ($data as $fields) {
                    fputcsv($fp, $fields);
                }
                fclose($fp);

                $headers = [
                        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
                    ,   'Content-type'        => 'text/csv'
                    ,   'Content-Disposition' => 'attachment; filename=galleries.csv'
                    ,   'Expires'             => '0'
                    ,   'Pragma'              => 'public'
                ];

                return response()->download('file.csv');
                dd("");
                Excel::create($this->county, function($excel) use($data) {

                    $excel->sheet($sitename, function($sheet) use($data) {

                        $sheet->fromArray($data);

                    });

                })->export('xls');
            }
        }
        dd("");

        $file = public_path().'/history-files/'.$file_name.'.json';
        $file = public_path().'/history-files/'.'103年斗六站_20150324.json';

        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);
            dd($data);
            // try {
            //     $this->store($data);
            //     File::delete($file);
            //     return "成功寫入".$request->input('file');
            // } catch (Exception $e) {
            //     return "該檔案有問題";
            // }
        } else {
            return "找不到檔案";
        }
    }

    public function table(Request $request)
    {
        $this->year = $request->input("year")-1911;
        $sitename = $request->input("sitename");
        $file_name = $this->year.'年'.$sitename.'站.json';
        $file = public_path().'/history-files/'.$file_name;
        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);
            $keys_str = "";
            $data_str = ["", "", "", "", "", "", "", "", "", "", "", ""];
            
            $keys_str .= "<tr>";
            foreach ($data[0] as $key => $value) {
                $keys_str .= "<th>$key</th>";
            }
            $keys_str .= "</tr>";

            foreach ($data as $key => $value) {
                $i = intval(explode('-', $value["PublishTime"])[1])-1;
                
                $data_str[$i] .= "<tr>";
                foreach ($value as $k => $v) {
                    $data_str[$i] .= "<td>$v</td>";
                }
                $data_str[$i] .= "</tr>";
            }
            return compact('keys_str', 'data_str');
        } else {
            return "檔案不存在";
        }
    }
    
    /**
     * Data process.
     *
     * @param Request $request
     */
    // public function export(Request $request)
    // {
    //     $this->year = $request->input("year");
    //     $this->county = $request->input("county");
    //     $this->sitename = $request->input("sitename");
    //     $this->output = $request->input("output_data");

    //     $result = ($this->county !== NULL) ? $this->getExcelData('county') : $this->getExcelData('sitename');
        
    //     array_push($this->xls_row, 'sitename');
    //     if (empty($this->output)) {
    //         array_push($this->xls_row, 'pm25');
    //     } else {
    //         foreach ($this->output as $key => $value) {
    //             array_push($this->xls_row, $value);
    //         }
    //     }
    //     array_push($this->xls_row, 'publish_time');

    //     $i = 0;
    //     foreach ($result as $key => $value) {
    //         $this->xls_data[$i] = [];
    //         foreach ($this->xls_row as $k => $v) {
    //             array_push($this->xls_data[$i], $value[$v]);
    //         }
    //         $i++;
    //     }
    //     dd($this->xls_data);
    //     $result = ($this->county !== NULL) ? $this->createExcel($this->county, 'xls') : $this->createExcel($this->sitename, 'xls');
    // }

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
