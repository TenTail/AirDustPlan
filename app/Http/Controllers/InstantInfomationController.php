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
        return view("instant_info.index");
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
        // $data = $request->id;
        // dd($data);
        // $now = Carbon::now();
        // $time = $now->year.'-'.sprintf("%02d", $now->month).'-'.sprintf("%02d", $now->day).' '.sprintf("%02d", $now->hour).':'.'00';
        // // echo($time);
        // $req = DB::table('airpollutions')->select('pm25')
        //             ->where('publish_time', $time)
        //             ->where('country', $data)
        //             ->get();

        $url = "http://opendata.epa.gov.tw/ws/Data/AQX/?format=json";
        $data = file_get_contents($url);
        $data = json_decode($data);
        $req = [];

        foreach ($data as $key => $value) {
            if(preg_match($request->id, $key->county)){

            }
        }
        return json($req);
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
