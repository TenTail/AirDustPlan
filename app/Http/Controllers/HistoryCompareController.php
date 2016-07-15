<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Carbon\Carbon;
use DB;

class HistoryCompareController extends Controller
{
    public function index()
    {
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

        $result2 = DB::select("
            SELECT `pm25`, `publish_time`
            FROM `airpollutions`
            WHERE `sitename` = '$sitename' AND `publish_time` LIKE '$r_timer2' 
            ORDER BY `publish_time`
        ");
        
        for ($i=0, $ptr1=0, $ptr2=0; $s_timer1 != $e_timer1; ) { 
            // data1
            $check_time = $s_timer1->year."-".$month."-".sprintf("%02d", $s_timer1->day)." ".sprintf("%02d", $s_timer1->hour).":00";
            if ($ptr1 < count($result1) && str_split($result1[$ptr1]->publish_time, 16)[0] == $check_time) {
                $data1[$i][0] = mktime($s_timer1->hour,$s_timer1->minute,0,$s_timer1->month,$s_timer1->day,$s_timer1->year)*1000;
                $data1[$i][1] = ($result1[$ptr1]->pm25 == -1) ? 0 : $result1[$ptr1]->pm25;
            } else {
                $data1[$i][0] = mktime($s_timer1->hour,$s_timer1->minute,0,$s_timer1->month,$s_timer1->day,$s_timer1->year)*1000;
                $data1[$i][1] = 0;
                $ptr1--;
            }

            // data2
            $check_time = $s_timer2->year."-".$month."-".sprintf("%02d", $s_timer2->day)." ".sprintf("%02d", $s_timer2->hour).":00";
            if ($ptr2 < count($result2) && str_split($result2[$ptr2]->publish_time, 16)[0] == $check_time) {
                $data2[$i][0] = mktime($s_timer1->hour,$s_timer1->minute,0,$s_timer1->month,$s_timer1->day,$s_timer1->year)*1000;
                $data2[$i][1] = ($result2[$ptr2]->pm25 == -1) ? 0 : $result2[$ptr2]->pm25;
            } else {
                $data2[$i][0] = mktime($s_timer1->hour,$s_timer1->minute,0,$s_timer1->month,$s_timer1->day,$s_timer1->year)*1000;
                $data2[$i][1] = 0;
                $ptr2--;
            }

            // next turn
            $i++;
            $ptr1++;
            $ptr2++;
            $s_timer1->addHour();
            $s_timer2->addHour();
        }

        return array($data1, $data2);
    }
}
