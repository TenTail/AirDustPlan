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
        return view("instant_info.index")->with('data', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // $now = Carbon::now();
        // $time = $now->year.'-'.sprintf("%02d", $now->month).'-'.sprintf("%02d", $now->day).' '.sprintf("%02d", $now->hour).':'.'00';
        // // echo($time);
        // $req = DB::table('airpollutions')->select('pm25')
        //             ->where('publish_time', $time)
        //             ->where('country', $data)
        //             ->get();

        // $county = $request['county'];
        // if($county == null) {
        //     $county = '雲林縣';
        // }
        // // dd($county);

        // $url = "http://opendata.epa.gov.tw/ws/Data/AQX/?format=json";
        // $json_data = file_get_contents($url);
        // $json_data = json_decode($json_data);
        // $req = array();

        // foreach ($json_data as $key => $value) {
        //     if($value->County == $county) {
        //         array_push($req, array("sitename" => $value->SiteName, "county" => $value->County, "psi" => $value->PSI, "publish_time" => $value->PublishTime));
        //     }
        // }

        $sitename = $request['sitename'];
        if($sitename == null) {
            $sitename = '斗六';
        }
        // dd($county);

        $url = "http://opendata.epa.gov.tw/ws/Data/AQX/?format=json";
        $json_data = file_get_contents($url);
        $json_data = json_decode($json_data);
        $req = array();

        foreach ($json_data as $key => $value) {
            // if($value->SiteName == $sitename) {
                array_push($req, array("sitename" => $value->SiteName, "county" => $value->County, "psi" => $value->PSI, "publish_time" => $value->PublishTime));
            // }
        }

        return response()->json($req);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
