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

    private $TW_to_ENG = array('基隆'=>'Keelung',
        '嘉義'=>'Chiayi',
        '美濃'=>'Mino',
        '大寮'=>'Great_Liao',
        '橋頭'=>'Bridgehead',
        '仁武'=>'Ren_Wu',
        '鳳山'=>'Fengshan',
        '林園'=>'Forest_Park',
        '楠梓'=>'Nan_Zi',
        '左營'=>'Left_camp',
        '前金'=>'Before_the_gold',
        '前鎮'=>'Before_the_town',
        '小港'=>'Small_port',
        '復興'=>'revival',
        '汐止'=>'Mizuki',
        '萬里'=>'Miles',
        '新店'=>'New_store',
        '土城'=>'Tucheng',
        '板橋'=>'Itabashi',
        '新莊'=>'New_Village',
        '菜寮'=>'Cai_Liu',
        '林口'=>'Linkou',
        '淡水'=>'freshwater',
        '三重'=>'triple',
        '永和'=>'Yonghe',
        '士林'=>'Shihlin',
        '中山'=>'Zhongshan',
        '萬華'=>'Wanhua',
        '古亭'=>'Guting',
        '松山'=>'Matsuyama',
        '大同'=>'Datong',
        '陽明'=>'Yangming',
        '桃園'=>'Taoyuan',
        '大園'=>'Large_garden',
        '觀音'=>'Guanyin',
        '平鎮'=>'Flat_town',
        '龍潭'=>'Longtan',
        '中壢'=>'Chungli',
        '湖口'=>'Hukou',
        '竹東'=>'Bamboo_East',
        '新竹'=>'Hsinchu',
        '頭份'=>'Head',
        '苗栗'=>'Miaoli',
        '三義'=>'Three_meanings',
        '豐原'=>'Fengyuan',
        '沙鹿'=>'Sand_deer',
        '大里'=>'Big_ri',
        '忠明'=>'Zhong_Ming',
        '西屯'=>'Xitun',
        '彰化'=>'Changhua',
        '線西'=>'Line_West',
        '二林'=>'Second_forest',
        '南投'=>'Nantou',
        '竹山'=>'Zhushan',
        '埔里'=>'Puli',
        '斗六'=>'Bucket_six',
        '崙背'=>'Lun_back',
        '臺西'=>'Taiwan',
        '麥寮'=>'Wheat_laos',
        '台西'=>'Taiwan',
        '新港'=>'Newport',
        '朴子'=>'Pu_child',
        '新營'=>'The_new_camp',
        '善化'=>'Good',
        '安南'=>'Annan',
        '臺南'=>'Tainan',
        '台南'=>'Tainan',
        '屏東'=>'Pingtung',
        '潮州'=>'Chaozhou',
        '恆春'=>'Hengchun',
        '臺東'=>'Taitung',
        '關山'=>'Guanshan',
        '台東'=>'Taitung',
        '宜蘭'=>'Ilan',
        '冬山'=>'Winter_Hill',
        '花蓮'=>'Hualien',
        '馬公'=>'Ma_Gong',
        '馬祖'=>'Matsu',
        '金門'=>'Kinmen',
    );

    // 所需的欄位
    private $keys = ['AMB_TEMP','CO','NO','NO2','NOx','O3','PM10','PM25','RAINFALL','RH','SO2','UVB','WD_HR','WIND_DIREC','WIND_SPEED','WS_HR','SiteName','PublishTime'];

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
        $file_name = $this->year.$this->TW_to_ENG[$sitename];
        $file = public_path().'/history-files/'.$this->year.'j/'.$file_name.'.json';
        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);
            
            // download json
            if ($request->input("export") == "JSON檔下載") 
                return response()->download($this->year.$sitename.'.json');

            // create csv file
            $tp_filename = time();
            $fp = fopen($tp_filename.'.csv', 'w');
            $col = [];
            fputcsv($fp, $this->keys);
            foreach ($data as $fields) {
                foreach ($this->keys as $k => $key) {
                    if (array_key_exists($key, $fields)) {
                        $col[$key] = $fields[$key];
                    } else {
                        $col[$key] = '';
                    }
                }
                fputcsv($fp, $col);
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
            }, 'UTF-8')->setFileName($this->year.$sitename)->convert($file_type);
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
        $file_name = $this->year.$this->TW_to_ENG[$sitename].'.json';
        $file = public_path().'/history-files/'.$this->year.'j/'.$file_name;
        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);

            $keys_str = ""; // table head
            $data_str = ["", "", "", "", "", "", "", "", "", "", "", ""]; // table body
            
            $keys_str .= "<tr>";
            foreach ($this->keys as $k => $key) {
                $keys_str .= "<th>$key</th>";
            }
            $keys_str .= "</tr>";

            foreach ($data as $value) {
                $i = intval(explode('-', $value["PublishTime"])[1])-1;

                $data_str[$i] .= "<tr>";
                foreach ($this->keys as $k => $key) {
                    if (array_key_exists($key, $value)) {
                        $data_str[$i] .= "<td>$value[$key]</td>";
                    } else {
                        $data_str[$i] .= "<td></td>";
                    }
                }
                $data_str[$i] .= "</tr>";
            }
            return compact('keys_str', 'data_str');
        } else {
            return "檔案不存在";
        }
    }
}
