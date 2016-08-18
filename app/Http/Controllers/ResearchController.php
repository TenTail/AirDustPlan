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
     */
    private $r_sitename = ['斗六', '宜蘭', '萬里', '淡水'];

    /**
     * 測站資料
     */
    private $r_sum_data = array(
            '斗六' => array(),
            '宜蘭' => array(),
            '萬里' => array(),
            '淡水' => array(),
        );

    /**
     * 測站資料
     */
    private $r_avg_data = array(
            '斗六' => array(),
            '宜蘭' => array(),
            '萬里' => array(),
            '淡水' => array(),
        );

    /**
     * 研究年份
     */
    private $r_year = ['2000', '2001', '2002', '2003', '2004', '2005', '2006', '2007', '2008', '2009', '2010', '2011', '2012', '2013', '2014', '2015'];
    
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
        $result = DB::select('
            SELECT `sitename`, SUM(`pm25`) AS SUM_PM25, AVG(`pm25`) AS AVG_PM25, SUBSTR(`publish_time`, 1, 7) AS p_time
            FROM (SELECT * FROM `airpollutions` WHERE `sitename` IN ("斗六", "宜蘭", "萬里", "淡水")) AS Table1
            WHERE (`publish_time` LIKE "2014%") AND `pm25` > 0
            GROUP BY  `sitename`, SUBSTR(`publish_time`, 1, 7)
            ');

        $month = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
        foreach ($result as $key => $value) {
            $this->distribution($value->sitename, $value->SUM_PM25, $value->AVG_PM25, $value->p_time);
        }

        $data = [
            'r_sum_data' => $this->r_sum_data,
            'r_avg_data' => $this->r_avg_data
        ];
        // dd(json_encode($this->r_sum_data['斗六']));
        return view('research.average', $data);
        // return view('research.average')->with('r_sum_data', $this->r_sum_data);
    }

    /**
     * 分配資料
     */
    public function distribution($sitename, $sum, $avg, $p_time)
    {
        array_push($this->r_sum_data[$sitename], [$p_time, $sum]);
        array_push($this->r_avg_data[$sitename], [$p_time, $avg]);
    }
}
