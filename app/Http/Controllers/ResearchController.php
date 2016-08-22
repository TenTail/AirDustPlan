<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Carbon\Carbon;
use App\Models\AirPollution;
use DB;

class ResearchController extends Controller
{
    /**
     * 研究的測站
     *
     * @var array
     */
    private $r_sitename = ['斗六', '宜蘭', '萬里', '淡水'];

    /**
     * 測站月加總資料
     *
     * @var array
     */
    private $r_sum_data = array(
            '斗六' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            ),
            '宜蘭' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            ),
            '萬里' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            ),
            '淡水' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            )
        );

    /**
     * 測站月平均資料
     *
     * @var array
     */
    private $r_avg_data = array(
            '斗六' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            ),
            '宜蘭' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            ),
            '萬里' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            ),
            '淡水' => array(
                '2010' => array(),'2011' => array(),'2012' => array(),'2013' => array(),'2014' => array(),'2015' => array(),
            )
        );

    /**
     * 研究年份
     *
     * @var int
     */
    private $r_year;
    
    /**
     * home
     */
    public function index()
    {
        return view('research.index');
    }

    /**
     * average page.
     */
    public function average()
    {
        $result = DB::select("
            SELECT t1.sitename, SUM(`pm25`) AS SUM_PM25, AVG(`pm25`) AS AVG_PM25,SUBSTR(t1.publish_time, 1, 7) AS p_time
            FROM `airpollutions` as t1
            inner join ( SELECT `id` FROM `airpollutions` WHERE `sitename` IN ('斗六', '宜蘭', '萬里', '淡水') AND `pm25` > 0 ) as t2
            on t1.id=t2.id AND SUBSTR(t1.publish_time, 1, 4) IN ('2010', '2011', '2012', '2013', '2014', '2015')
            GROUP BY t1.sitename, SUBSTR(t1.publish_time, 1, 7)
            ORDER BY SUBSTR(t1.publish_time, 1, 7) ASC 
            ");
        
        foreach ($result as $key => $value) {
            $this->distribution($value->sitename, $value->SUM_PM25, $value->AVG_PM25, $value->p_time);
        }
        
        $data = [
            'r_sum_data' => $this->r_sum_data,
            'r_avg_data' => $this->r_avg_data
        ];
        
        return view('research.average', $data);
    }

    /**
     * 分配資料
     */
    public function distribution($sitename, $sum, $avg, $p_time)
    {
        $month = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        if (count($this->r_sum_data[$sitename][substr($p_time, 0, 4)]) < 12) {
            while ($month[count($this->r_sum_data[$sitename][substr($p_time, 0, 4)])] != substr($p_time, 5, 2)) {
                $f_time = substr($p_time, 0, 4)."-".$month[count($this->r_sum_data[$sitename][substr($p_time, 0, 4)])];
                array_push($this->r_sum_data[$sitename][substr($p_time, 0, 4)], [$f_time, null]);
                array_push($this->r_avg_data[$sitename][substr($p_time, 0, 4)], [$f_time, null]);
            }
        }
        array_push($this->r_sum_data[$sitename][substr($p_time, 0, 4)], [$p_time, $sum]);
        array_push($this->r_avg_data[$sitename][substr($p_time, 0, 4)], [$p_time, $avg]);
    }

    /**
     * 空汙超標日子
     */
    public function excessive()
    {
        $result = DB::select("
            SELECT t1.sitename, AVG(t1.pm25) AS AVG_PM25, SUBSTR(t1.publish_time, 1, 4) AS year, SUBSTR(t1.publish_time, 6, 2) AS month, SUBSTR(t1.publish_time, 9, 2) AS day
            FROM `airpollutions` AS t1
            inner join (SELECT `id` FROM `airpollutions` WHERE `publish_time` LIKE '2015%' AND `sitename` = '淡水' AND `pm25` > 0) AS t2
            ON t1.id = t2.id 
            GROUP BY t1.sitename, SUBSTR(t1.publish_time, 1, 10)
            ORDER BY SUBSTR(t1.publish_time, 1, 10) ASC
            ");

        $level1 = [];
        $level2 = [];
        $level3 = [];
        $level4 = [];
        $level5 = [];

        foreach ($result as $key => $value) {
            $time = $value->month."/".$value->day."/".$value->year;
            switch ($this->aqiLevel($value->AVG_PM25)) {
                case 1:
                    $level1[$time] = $value->AVG_PM25;
                    break;
                case 2:
                    $level2[$time] = $value->AVG_PM25;
                    break;
                case 3:
                    $level3[$time] = $value->AVG_PM25;
                    break;
                case 4:
                    $level4[$time] = $value->AVG_PM25;
                    break;
                case 5:
                    $level5[$time] = $value->AVG_PM25;
                    break;
                default:
                    break;
            }
        }

        $data = [
            'level1' => $level1,
            'level2' => $level2,
            'level3' => $level3,
            'level4' => $level4,
            'level5' => $level5,
        ];

        return view('research.excessive', $data);
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
