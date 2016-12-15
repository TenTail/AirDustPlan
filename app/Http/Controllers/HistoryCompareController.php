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
        $file = public_path().'/history-files/92j/92Bucket_six.json';
        if (File::exists($file)) {
            $file_content = file_get_contents($file);
            $data = json_decode($file_content, true);
        } else {
            dd("not find file.");
        }
        
        $collection = collect($data);
        $key = $collection->flatMap(function ($item) {
            if ($item['PublishTime'] >= '2003-01' AND $item['PublishTime'] < '2003-03') 
            return [$item['PublishTime'] => ['pm10' => $item['PM10'],'PublishTime' => explode(" ", $item['PublishTime'])[0]]];
        })
        ->sortBy(function ($si, $sk) {
            return $sk;
        })
        ->groupBy('PublishTime')
        ->flatMap(function ($fi, $fk) {
            return [$fk => $fi->avg('pm10')];
        });

        dd($key);
        return view('history-compare2');
    }

    public function compare2(Request $request)
    {

        // $t = $year."-".$month."-01 00:00";
        $t = "2014-01-01 00:00";
        $sitename = $request->input("sitename");
        $pollution = strtolower($request->input("pollution")[0]);
        $pollution = ($pollution == 'pm2.5') ? 'pm25' : $pollution;

        // data1
        $s_timer1 = Carbon::createFromFormat("Y-m-d H:i", $t);
        $e_timer1 = Carbon::createFromFormat("Y-m-d H:i", $t)->addMonths(2);
        // $r_timer1 = $s_timer1->year."-".$month."-%";
        $data1 = array();

        // SELECT `pm25`, `publish_time` FROM `airpollutions` WHERE `sitename` = '斗六' AND `publish_time` BETWEEN '2015-01' AND '2015-03' OR `publish_time` BETWEEN '2014-01' AND '2014-03' ORDER BY `publish_time`
        $result1 = DB::select("
            SELECT `$pollution`, `publish_time`
            FROM `airpollutions`
            WHERE `sitename` = '$sitename' AND `publish_time` BETWEEN '2014-01' AND '2014-03'
            ORDER BY `publish_time`
        ");
        $result1 = DB::select("
            SELECT t1.sitename, AVG(t1.$pollution) AS $pollution, SUBSTR(t1.publish_time, 1, 4) AS year, SUBSTR(t1.publish_time, 6, 2) AS month, SUBSTR(t1.publish_time, 9, 2) AS day
            FROM `airpollutions` AS t1
            inner join (SELECT `id` FROM `airpollutions` WHERE `publish_time` BETWEEN '2014-01' AND '2014-03' AND `sitename` = '$sitename' AND `$pollution` > 0) AS t2
            ON t1.id = t2.id 
            GROUP BY t1.sitename, SUBSTR(t1.publish_time, 1, 10)
            ORDER BY SUBSTR(t1.publish_time, 1, 10) ASC
        ");

        $i = 0;
        for ($ptr1=0; $s_timer1 != $e_timer1; ) { 
            $time = mktime($s_timer1->hour,$s_timer1->minute,0,$s_timer1->month,$s_timer1->day,$s_timer1->year)*1000;

            // data1
            $check_time = $s_timer1->year."-".sprintf("%02d", $s_timer1->month)."-".sprintf("%02d", $s_timer1->day)." ".sprintf("%02d", $s_timer1->hour).":00";
            
            // if ($ptr1 < count($result1) && $result1[$ptr1]->publish_time == $check_time) {
            if ($ptr1 < count($result1) AND $result1[$ptr1]->year."-".$result1[$ptr1]->month."-".$result1[$ptr1]->day." 00:00" == $check_time) {
                $data1[$i][0] = $time;
                $data1[$i][1] = ($result1[$ptr1]->$pollution == -1 || $result1[$ptr1]->$pollution == 0) ? null : $result1[$ptr1]->$pollution;
                
            } else {
                $data1[$i][0] = $time;
                $data1[$i][1] = null;
                $ptr1--;
            }

            // next turn
            $i++;
            $ptr1++;
            $s_timer1->addDay();
        }

        return $data1;
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
