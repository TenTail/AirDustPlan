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
     * @var array
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

    public function index()
    {
        return view("excel_export.index");
    }

    /**
     * Export json|csv|xls .
     *
     * @param Request $request
     */
    public function export(Request $request)
    {
        $this->year = $request->input("year")-1911;
        $sitename = $request->input("sitename");
        $file_name = $this->year.'年'.$sitename.'站';
        $file = public_path().'/history-files/'.$file_name.'.json';
        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);
            
            // download json
            if ($request->input("export") == "JSON檔下載") 
                return response()->download($file);

            // create csv file
            $tp_filename = time();
            $fp = fopen($tp_filename.'.csv', 'w');
            $col = [];
            foreach ($data[0] as $key => $value) {

                array_push($col, $key);
            }
            fputcsv($fp, $col);
            foreach ($data as $fields) {
                fputcsv($fp, $fields);
            }
            fclose($fp);

            // check file_type
            if ($request->input("export") == "CSV檔下載") {
                $file_type = 'CSV';
            } else {
                $file_type = 'xls';
            }

            // download and delete $tp_filename
            Excel::load($tp_filename.'.csv', function ($reader) use($tp_filename) {
                File::delete(public_path().'/'.$tp_filename.'.csv');    
            }, 'UTF-8')->setFileName($file_name)->convert($file_type);
        }
    }

    /**
     * previews table.
     *
     * @param Request $request
     * @return array|string
     */
    public function table(Request $request)
    {
        $this->year = $request->input("year")-1911;
        $sitename = $request->input("sitename");
        $file_name = $this->year.'年'.$sitename.'站.json';
        $file = public_path().'/history-files/'.$file_name;
        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);

            $keys_str = ""; // table head
            $data_str = ["", "", "", "", "", "", "", "", "", "", "", ""]; // table body
            
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
}
