<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Carbon\Carbon;
use DB;
use File;

class HistoryCompareController extends Controller
{
    /**
     * PM2.5 convert to AQI.
     *
     * @var array
     */
    private $pm25_to_aqi = array(
        ['pm25l' => 0, 'pm25h' => 15, 'aqil' => 0, 'aqih' => 50],
        ['pm25l' => 15, 'pm25h' => 40, 'aqil' => 50, 'aqih' => 100],
        ['pm25l' => 40, 'pm25h' => 65, 'aqil' => 100, 'aqih' => 150],
        ['pm25l' => 65, 'pm25h' => 150, 'aqil' => 150, 'aqih' => 200],
        ['pm25l' => 150, 'pm25h' => 250, 'aqil' => 200, 'aqih' => 300],
        ['pm25l' => 250, 'pm25h' => 500, 'aqil' => 300, 'aqih' => 500]
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

    public function index()
    {
        // dd($this->convertAQI(0));
        return view('history-compare');
    }

    public function compare(Request $request)
    {
        $year = $request->input("year");
        $month = $request->input("month");
        $sitename = $request->input("sitename");

        $t = $year."-".$month."-01 00:00";

        // data1
        $s_timer1 = Carbon::createFromFormat("Y-m-d H:i", $t);
        $e_timer1 = Carbon::createFromFormat("Y-m-d H:i", $t)->addMonth();
        $r_timer1 = $s_timer1->year."-".$month."-%";
        $data1 = array();
        $aqi_data1 = array();

        $result1 = DB::select("
            SELECT `pm25`, `publish_time`
            FROM `airpollutions`
            WHERE `sitename` = '$sitename' AND `publish_time` LIKE '$r_timer1' 
            ORDER BY `publish_time`
        ");

        // data2
        $s_timer2 = Carbon::createFromFormat("Y-m-d H:i", $t)->subYear();
        $e_timer2 = Carbon::createFromFormat("Y-m-d H:i", $t)->subYear()->addMonth();
        $r_timer2 = $s_timer2->year."-".$month."-%";
        $data2 = array();
        $aqi_data2 = array();

        // avg
        $avg1 = 0;
        $avg2 = 0;
        $a1 = 0;
        $a2 = 0;

        $result2 = DB::select("
            SELECT `pm25`, `publish_time`
            FROM `airpollutions`
            WHERE `sitename` = '$sitename' AND `publish_time` LIKE '$r_timer2' 
            ORDER BY `publish_time`
        ");

        $i=0;
        for ($ptr1=0, $ptr2=0; $s_timer1 != $e_timer1; ) { 
            $time = mktime($s_timer1->hour,$s_timer1->minute,0,$s_timer1->month,$s_timer1->day,$s_timer1->year)*1000;

            // data1 and aqi_data1
            $check_time = $s_timer1->year."-".$month."-".sprintf("%02d", $s_timer1->day)." ".sprintf("%02d", $s_timer1->hour).":00";
            if ($ptr1 < count($result1) && str_split($result1[$ptr1]->publish_time, 16)[0] == $check_time) {
                $data1[$i][0] = $time;
                $data1[$i][1] = ($result1[$ptr1]->pm25 == -1 || $result1[$ptr1]->pm25 == 0) ? null : $result1[$ptr1]->pm25;
                $aqi_data1[$i][0] = $time;
                $aqi_data1[$i][1] = ($data1[$i][1] == null) ? null : round($this->convertAQI($data1[$i][1]), 1);
                if ($data1[$i][1] != null) {
                    $avg1 = $avg1+$data1[$i][1];
                    $a1++;
                }
            } else {
                $data1[$i][0] = $time;
                $data1[$i][1] = null;
                $aqi_data1[$i][0] = $time;
                $aqi_data1[$i][1] = null;
                $ptr1--;
            }

            // data2 and aqi_data2
            $check_time = $s_timer2->year."-".$month."-".sprintf("%02d", $s_timer2->day)." ".sprintf("%02d", $s_timer2->hour).":00";
            if ($ptr2 < count($result2) && str_split($result2[$ptr2]->publish_time, 16)[0] == $check_time) {
                $data2[$i][0] = $time;
                $data2[$i][1] = ($result2[$ptr2]->pm25 == -1 || $result2[$ptr2]->pm25 == 0) ? null : $result2[$ptr2]->pm25;
                $aqi_data2[$i][0] = $time;
                $aqi_data2[$i][1] = ($data2[$i][1] == null) ? null : round($this->convertAQI($data2[$i][1]), 1);
                if ($data2[$i][1] != null) {
                    $avg2 = $avg2+$data2[$i][1];
                    $a2++;
                }
            } else {
                $data2[$i][0] = $time;
                $data2[$i][1] = null;
                $aqi_data2[$i][0] = $time;
                $aqi_data2[$i][1] = null;
                $ptr2--;
            }

            // next turn
            $i++;
            $ptr1++;
            $ptr2++;
            $s_timer1->addHour();
            $s_timer2->addHour();
        }

        $avg3 = DB::select("
            SELECT `sitename`, AVG(`pm25`) AS 'pm25'
            FROM `airpollutions`
            WHERE `publish_time` LIKE '$r_timer1' AND `sitename` IN ('淡水','宜蘭','萬里')
            GROUP BY `sitename`
            ");

        $avg = [($a1 == 0) ? 0 : $avg1/$a1, ($a2 == 0) ? 0 : $avg2/$a2];

        $s = ['淡水','宜蘭','萬里'];
        for ($i=0, $button=0; $i < 3; $i++) { 
            for ($j=0; $j < count($avg3); $j++) { 
                if ($avg3[$j]->sitename == $s[$i]) {
                    array_push($avg, $avg3[$i]->pm25);
                    $button = 1;
                    break;
                }
            }
            if ($button == 0) {
                array_push($avg, null);
            } else {
                $button = 0;
            }
        }
        // array_search('淡水', $)
        // array_search('宜蘭', $)
        // array_search('萬里', $)

        return array($data1, $data2, $aqi_data1, $aqi_data2, $avg);
    }

    public function index2()
    {
        return view('history-compare2');
    }

    public function compare2(Request $request)
    {
        $month = sprintf("%02d", $request->input("month"));
        // 格式['2001', 90, '2001-01-01 00:00', '2001-03-01 00:00']
        $year = array_map(function ($y) use ($month) {
            return [$y, intval($y)-1911, $y."-".$month."-01 00:00", $y."-".sprintf("%02d", intval($month)+2)."-01 00:00"];
        }, $request->input("year"));
        $sitename = $request->input("sitename");
        $pollution = ($request->input("pollution")[0] == 'pm2.5') ? 'pm25' : $request->input("pollution")[0];
        
        // 根據 $year 與 $sitename 抓取檔案，並解析檔案然後轉換成 collection
        $collections = array_map(function ($y) use ($sitename) {
            $path = public_path().'/history-files/'.$y[1].'j/'.$y[1].$this->TW_to_ENG[$sitename].'.json';
            return File::exists($path) 
                ? collect(json_decode(file_get_contents($path), true))
                : NULL;
        }, $year);

        $data = array();
        foreach ($collections as $i => $value) {
            if ($value == NULL) continue; // 如果是NULL代表找不到檔案
            $temp = $value->flatMap(function ($item) use ($i, $year, $pollution) { // 選取出正確的月份與空汙測項
                if ($item['PublishTime'] >= substr($year[$i][2], 0, 7) AND $item['PublishTime'] < substr($year[$i][3], 0, 7)) 
                return [
                    $item['PublishTime'] => [
                        $pollution => array_key_exists(strtoupper($pollution), $item)
                            ? $item[strtoupper($pollution)] : NULL,
                        'PublishTime' => explode(" ", $item['PublishTime'])[0]
                    ]
                ];
            })
            ->sortBy(function ($si, $sk) { // 根據 key值 去做排序
                return $sk;
            })
            ->groupBy('PublishTime') // 把同一天群組起來
            ->flatMap(function ($fi, $fk) use ($pollution) { // 將空汙測項根據每天做平均
                $date = explode('-', $fk);
                return array([mktime(0,0,0,$date[1],$date[2],0)*1000, round($fi->avg($pollution), 2)]);
            })
            ->toArray(); // collection 轉換成 array
            
            // highcharts 的 series 資料格式
            array_push($data, [
                'name' => $year[$i][0]."年",
                'data' => $temp,
                'type' => 'spline',
                'tooltip' => [
                    'valueSuffix' => ' μg/m3'
                ]
            ]);
        }

        return $data;
    }

    /**
     * Return value that PM2.5 convert to AQI.
     *
     * @return double
     */
    public function convertAQI($pm25_value)
    {
        $level = $this->aqiLevel($pm25_value);
        $temp = $this->pm25_to_aqi[$level-1];
        if ($pm25_value == 0) {
            return 0;
        }
        return (($temp['aqih']-$temp['aqil'])/($temp['pm25h']-$temp['pm25l']))*($pm25_value-$temp['pm25l'])+$temp['aqil'];
    }

    /**
     * Return AQI Level.
     *
     * @return int
     */
    public function aqiLevel($pm25_value)
    {
        if ($pm25_value <= 15) {
            return 1;
        } elseif ($pm25_value > 15 && $pm25_value <= 40) {
            return 2;
        } elseif ($pm25_value > 40 && $pm25_value <= 65) {
            return 3;
        } elseif ($pm25_value > 65 && $pm25_value <= 150) {
            return 4;
        } elseif ($pm25_value > 150 && $pm25_value <= 250) {
            return 5;
        } elseif ($pm25_value > 250) {
            return 6;
        }
    }
}
