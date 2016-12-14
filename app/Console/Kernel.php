<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Carbon\Carbon;
use DB;
use Mail;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        \App\Console\Commands\TestLog::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('email:Log')
                ->call(function() {
            $now = Carbon::now('Asia/Taipei');
            $h_now = $now->format('Y-m-d h');
            $h_now = $h_now.'00';
            $s_now = $now->subhour()->format('Y-m-d h');
            $s_now = $s_now.'00';
            $query = DB::table('table')->select('sitename', 'publish_time')
                    ->where('publish_time', '=', $s_now);

            $url = "http://opendata.epa.gov.tw/ws/Data/AQX/?format=json";
            $data_exist = false;
            $site = ["臺東","臺南","臺西","觀音","關山","豐原","龍潭","頭份","橋頭","線西","潮州","鳳山","彰化","嘉義","萬華","萬里","楠梓","新營","新港","新莊","新店","新竹","陽明","菜寮","善化","湖口","復興","麥寮","淡水","崙背","基隆","馬祖","馬公","桃園","埔里","苗栗","美濃","恆春","屏東","南投","前鎮","前金","金門","花蓮","松山","板橋","林園","林口","忠明","宜蘭","沙鹿","西屯","竹東","竹山","汐止","朴子","安南","永和","平鎮","左營","古亭","冬山","斗六","仁武","中壢","中山","小港","大寮","大園","大里","大同","士林","土城","三義","三重","二林"];  

            /*
            * Check data had saved into database and data public_time is correct.
            */
            foreach($query as $key => $value) {
                for($i = 0 ; $i < count($site) ; $i++) {
                    if($query->sitename == $site[$i]) {
                        if($h_now != $value->publish_time) {
                            Mail::send('mail_test', ['user' => 'Robot'], function($m) use ($user) {
                                $m->from('maselab318pm25@gmail.com', 'Laravel');
                                $m->to($user->email, $user->name)->subject('PM2.5 Data do not receive!');
                            });
                        }
                        else {
                            $data_exist = true;
                        }
                    }
                    else {
                        Mail::send('mail_test', ['user' => 'Robot'], function($m) use ($user) { 
                            $m->from('maselab318pm25@gmail.com', 'Laravel');
                            $m->to($user->email, $user->name)->subject("PM2.5 Data don't exist!");                           
                        });
                    }
                }
            }

            /*
            * If data don't exist in the PM25 table, get data from gov.
            */
            if(!$data_exist) {
                $data = file_get_contents($url);
                $j_data = json_decode($data, true);

                foreach($j_data as $k => $v) {

                    DB::table('PM25')->insert(['sitename' => $v->SiteName, 'county' => $v->County, 'psi' => $v->PSI, 'so2' => $v->SO2, 'co' => $v->CO, 'pm10' => $v->PM10, 'pm25' => $k['PM2.5'], ' o3' => $v->O3, 'no2' => $v->NO2, 'wind_speed' => $v->WindSpeed, 'wind_direction' => $v->WindDirec, 'publish_time' => $v->PublishTime]);
                }   
            }
        })->everyMinute();
    }
}
