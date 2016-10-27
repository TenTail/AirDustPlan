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
        return view('research.excessive');
    }

    /**
     * 
     */
    public function excessiveGetData(Request $request)
    {
        $year = $request->input('year')."%";
        $sitename = $request->input('sitename');

        $result = DB::select("
            SELECT t1.sitename, AVG(t1.pm25) AS AVG_PM25, SUBSTR(t1.publish_time, 1, 4) AS year, SUBSTR(t1.publish_time, 6, 2) AS month, SUBSTR(t1.publish_time, 9, 2) AS day
            FROM `airpollutions` AS t1
            inner join (SELECT `id` FROM `airpollutions` WHERE `publish_time` LIKE '$year' AND `sitename` = '$sitename' AND `pm25` > 0) AS t2
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
            'year' => $request->input('year'),
            'level1' => json_encode($level1),
            'level2' => json_encode($level2),
            'level3' => json_encode($level3),
            'level4' => json_encode($level4),
            'level5' => json_encode($level5),
        ];

        return $data;
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

    public function check()
    {
        $check_year = [
            '2000-01-01 00:00',
            '2001-01-01 00:00',
            '2002-01-01 00:00',
            '2003-01-01 00:00',
            '2004-01-01 00:00',
            '2005-01-01 00:00',
            '2006-01-01 00:00',
            '2007-01-01 00:00',
            '2008-01-01 00:00',
            '2009-01-01 00:00',
            '2010-01-01 00:00',
            '2011-01-01 00:00',
            '2012-01-01 00:00',
            '2013-01-01 00:00',
            '2014-01-01 00:00',
            '2015-01-01 00:00',
        ];
        $all_site = [
            '基隆','嘉義','美濃','大寮','橋頭','仁武','鳳山',
            '林園','楠梓','左營','前金','前鎮','小港','復興',
            '汐止','萬里','新店','土城','板橋','新莊','菜寮',
            '林口','淡水','三重','永和','士林','中山','萬華',
            '古亭','松山','大同','陽明','桃園','大園','觀音',
            '平鎮','龍潭','中壢','湖口','竹東','新竹','頭份',
            '苗栗','三義','豐原','沙鹿','大里','忠明','西屯',
            '彰化','線西','二林','南投','竹山','埔里','斗六',
            '崙背','臺西','麥寮','新港','朴子','新營','善化',
            '安南','臺南','屏東','潮州','恆春','臺東','關山',
            '宜蘭','冬山','花蓮','馬公','馬祖','金門'
        ];
        $results = AirPollution::select('sitename', 'publish_time')
                               ->whereIn('publish_time', $check_year)
                               ->orderBy('publish_time', 'desc')
                               ->get()
                               ->toArray();
        $output = array();
        $not_in = true;
        foreach ($check_year as $key => $value) {
            $output[$value] = array();
            foreach ($all_site as $k => $v) {
                array_push($output[$value], $v);
            }
        }
        
        foreach ($results as $key => $value) {
            $in = array_search($value['sitename'], $output[$value['publish_time']]);
            if ($in !== false) {
                unset($output[$value['publish_time']][$in]);
                // array_push($output[$value['publish_time']], $value['sitename']);
            }
        }
        
        foreach ($output as $key => $datas) {
            // dd(sizeof($datas));
            echo explode('-', $key)[0]." 缺少了".sizeof($datas)."比資料。<br>";
            foreach ($datas as $key => $value) {
                echo $value." ";
            }
            echo "<br>";
        }
    }
}
