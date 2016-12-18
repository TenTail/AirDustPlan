<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\AirPollution;
use Carbon\Carbon;
use Mail;

class ParseOpenData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get PM2.5 PM2.5 Open Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $url = "http://opendata.epa.gov.tw/ws/Data/AQX/?format=json";
        $getdata = file_get_contents($url);
        $jdata = json_decode($getdata, true);
        $now = Carbon::now('Asia/Taipei');
        $lasthour = $now->subhour()->format('Y-m-d H');
        $lasthour = $lasthour.':00';
        $now_hour = $now->format('Y-m-d H');
        $now_hour = $now_hour.':00';

        $checkdata = DB::table('airpollutions')->select('sitename', 'publish_time')
                    ->where('publish_time', '=', $lasthour)
                    ->get();

        if(empty($checkdata)) {

            $data = ['name' => 'Not exist'];

            Mail::send('mail', $data, function($message) {
        
                $message->to('40243137@gm.nfu.edu.tw')->subject($lasthour . 'was not saved.');
             
             });
            \Log::info('Open Data was not saved at' . $lasthour);
        }

        $query = DB::table('airpollutions')->select('sitename', 'publish_time')
                ->where('publish_time', '=', $lasthour)
                ->get();

        if(empty($query)) {
            foreach($jdata as $value) {
                DB::table('airpollutions')->insert([
                    'sitename'       => $value['SiteName'],                    
                    'psi'            => $value['PSI'],
                    'pm25'           => $value['PM2.5'],
                    'county'         => $value['County'],
                    'so2'            => $value['SO2'],
                    'co'             => $value['CO'],
                    'o3'             => $value['O3'],
                    'pm10'           => $value['PM10'],
                    'no2'            => $value['NO2'],
                    'wind_speed'     => $value['WindSpeed'],
                    'wind_direction' => $value['WindDirec'],
                    'publish_time'   => $value['PublishTime']
                ]);
            }
            \Log::info('Open Data insert to DB success ' . $lasthour);
        }
        else {
            \Log::info('Open Data had existed in DB.');
        }
    
    }
}
