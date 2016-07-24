<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Carbon\Carbon;
use DB;

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

        $result2 = DB::select("
            SELECT `pm25`, `publish_time`
            FROM `airpollutions`
            WHERE `sitename` = '$sitename' AND `publish_time` LIKE '$r_timer2' 
            ORDER BY `publish_time`
        ");
        
        for ($i=0, $ptr1=0, $ptr2=0; $s_timer1 != $e_timer1; ) { 
            $time = mktime($s_timer1->hour,$s_timer1->minute,0,$s_timer1->month,$s_timer1->day,$s_timer1->year)*1000;

            // data1 and aqi_data1
            $check_time = $s_timer1->year."-".$month."-".sprintf("%02d", $s_timer1->day)." ".sprintf("%02d", $s_timer1->hour).":00";
            if ($ptr1 < count($result1) && str_split($result1[$ptr1]->publish_time, 16)[0] == $check_time) {
                $data1[$i][0] = $time;
                $data1[$i][1] = ($result1[$ptr1]->pm25 == -1) ? 0 : $result1[$ptr1]->pm25;
                $aqi_data1[$i][0] = $time;
                $aqi_data1[$i][1] = round($this->convertAQI($data1[$i][1]), 1);
            } else {
                $data1[$i][0] = $time;
                $data1[$i][1] = 0;
                $aqi_data1[$i][0] = $time;
                $aqi_data1[$i][1] = 0;
                $ptr1--;
            }

            // data2 and aqi_data2
            $check_time = $s_timer2->year."-".$month."-".sprintf("%02d", $s_timer2->day)." ".sprintf("%02d", $s_timer2->hour).":00";
            if ($ptr2 < count($result2) && str_split($result2[$ptr2]->publish_time, 16)[0] == $check_time) {
                $data2[$i][0] = $time;
                $data2[$i][1] = ($result2[$ptr2]->pm25 == -1) ? 0 : $result2[$ptr2]->pm25;
                $aqi_data2[$i][0] = $time;
                $aqi_data2[$i][1] = round($this->convertAQI($data2[$i][1]), 1);
            } else {
                $data2[$i][0] = $time;
                $data2[$i][1] = 0;
                $aqi_data2[$i][0] = $time;
                $aqi_data2[$i][1] = 0;
                $ptr2--;
            }

            // next turn
            $i++;
            $ptr1++;
            $ptr2++;
            $s_timer1->addHour();
            $s_timer2->addHour();
        }

        return array($data1, $data2, $aqi_data1, $aqi_data2);
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
