<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class InstantInfomationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array('臺北市','新北市','基隆市','桃園市','新竹市','新竹縣','苗栗縣','臺中市','彰化縣','南投縣','雲林縣', '嘉義縣','嘉義市','臺南市','高雄市','屏東縣', '宜蘭縣','花蓮縣','臺東縣','澎湖縣');
        $query = DB::table('airpollutions')->where('sitename', '斗六')->first();

        return view("instant_info.index")->with('data', $query);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $url = "http://opendata.epa.gov.tw/ws/Data/AQX/?format=json";
        $json_data = file_get_contents($url);
        $json_data = json_decode($json_data);
        $req = array();
        $icon_base = './img/icons/';
        $icon = ' ';

        foreach ($json_data as $key => $value) {
            /*
            * green
            */
            if($value->PSI < 51) {
                $icon = $icon_base.'green.png';
            }

            /*
            * yellow
            */
            if($value->PSI > 50 && $value->PSI < 101) {
                $icon = $icon_base.'yellow.png';
            }

            /*
            * orange
            */
            if($value->PSI > 100 && $value->PSI < 151) {
                $icon = $icon_base.'orange.png';
            }

            /*
            * red
            */
            if($value->PSI > 150 && $value->PSI < 201) {
                $icon = $icon_base.'red.png';
            }

            /*
            * purple
            */
            if($value->PSI > 199 && $value->PSI < 300) {
                $icon = $icon_base.'purple.png';
            }

            /*
            * brown
            */
            if($value->PSI > 299) {
                $icon = $icon_base.'brown.jpg';
            }

            if($value->PSI == null) {
                $icon = $icon_base.'black.jpg';
            }
            array_push($req, array("sitename" => $value->SiteName, "county" => $value->County, "psi" => $value->PSI, "publish_time" => $value->PublishTime, "icon" => $icon));

        }

        return response()->json($req);

    }

    public function show_past_12_hours_data(Request $request) {
        $sitename = $request['sitename'];
        $now = Carbon::now();

        /*
        * Carbon::create is bulit fo testing.
        */
        // $now = Carbon::create(2016, 05, 05, 01)->subHour(5);
        
        $data = [];

        /*
        * Format the $now & $past_12_hours
        */
        $fnow = $now->year.'-'.sprintf("%02d", $now->month).'-'.sprintf("%02d", $now->day).' '.sprintf("%02d", $now->hour).':'.'00';
        // $fpast = $past_12_hours->year.'-'.sprintf("%02d", $past_12_hours->month).'-'.sprintf("%02d", $past_12_hours->day).' '.sprintf("%02d", $past_12_hours->hour).':'.'00';

        for($i = 1 ; $i < 7 ; $i++) {
            try {
                $query = DB::table('airpolluctions')->select('sitename', 'pm25', 'psi', 'co', 'pm10', 'publish_time')
                    ->where('sitename', '=', $sitename)
                    ->where('publish_time', '=', $fnow)
                    ->get();
                array_push($data, $query[0]);
            }
            catch(Exception $e) {

            }
            
            $past = $now->addHour();          
            $now = $past;
            $fnow = $past->year.'-'.sprintf("%02d", $past->month).'-'.sprintf("%02d", $past->day).' '.sprintf("%02d", $past->hour).':'.'00';
        }

        return response()->json($data);

    }

}
