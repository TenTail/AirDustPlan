<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\AirPollution;

class UploadFilesController extends Controller
{
    /**
     * The original content of uploaded file.
     * 
     * @var string
     */
    private $file_content;

    /**
     * This array is used to transform sitename and county.
     *
     * @var array
     */
    private $site_to_county = array(
        '基隆'=>'基隆市',
        '嘉義'=>'嘉義市',
        '美濃'=>'高雄市','大寮'=>'高雄市','橋頭'=>'高雄市','仁武'=>'高雄市','鳳山'=>'高雄市','林園'=>'高雄市','楠梓'=>'高雄市','左營'=>'高雄市','前金'=>'高雄市','前鎮'=>'高雄市','小港'=>'高雄市','復興'=>'高雄市',
        '汐止'=>'新北市','萬里'=>'新北市','新店'=>'新北市','土城'=>'新北市','板橋'=>'新北市','新莊'=>'新北市','菜寮'=>'新北市','林口'=>'新北市','淡水'=>'新北市','三重'=>'新北市','永和'=>'新北市',
        '士林'=>'臺北市','中山'=>'臺北市','萬華'=>'臺北市','古亭'=>'臺北市','松山'=>'臺北市','大同'=>'臺北市','陽明'=>'臺北市',
        '桃園'=>'桃園市','大園'=>'桃園市','觀音'=>'桃園市','平鎮'=>'桃園市','龍潭'=>'桃園市','中壢'=>'桃園市',
        '湖口'=>'新竹縣','竹東'=>'新竹縣',
        '新竹'=>'新竹市',
        '頭份'=>'苗栗縣','苗栗'=>'苗栗縣','三義'=>'苗栗縣',
        '豐原'=>'臺中市','沙鹿'=>'臺中市','大里'=>'臺中市','忠明'=>'臺中市','西屯'=>'臺中市',
        '彰化'=>'彰化縣','線西'=>'彰化縣','二林'=>'彰化縣',
        '南投'=>'南投縣','竹山'=>'南投縣','埔里'=>'南投縣',
        '斗六'=>'雲林縣','崙背'=>'雲林縣','臺西'=>'雲林縣','麥寮'=>'雲林縣',
        '新港'=>'嘉義縣','朴子'=>'嘉義縣',
        '新營'=>'臺南市','善化'=>'臺南市','安南'=>'臺南市','臺南'=>'臺南市',
        '屏東'=>'屏東縣','潮州'=>'屏東縣','恆春'=>'屏東縣',
        '臺東'=>'臺東縣','關山'=>'臺東縣',
        '宜蘭'=>'宜蘭縣','冬山'=>'宜蘭縣',
        '花蓮'=>'花蓮縣',
        '馬公'=>'澎湖縣',
        '馬祖'=>'連江縣',
        '金門'=>'金門縣',
    );

    public function index()
    {
        return view('file_upload.index');
    }

    /**
     * 上傳檔案
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('file_json')) {
            $file = $request->file('file_json');
            $this->file_content = file_get_contents($file->getRealPath());
            $data = json_decode($this->file_content, true);
            $this->check($data);
            dd('success');
            // $fileName = $file->getClientOriginalName(); // file name
            // $request->file('file_json')->move($destinationPath, $fileName);
        } else {
            dd("上傳失敗");
        }
    }

    /**
     * 查看資料是否重複.
     *
     * @param array $data
     */
    public function check($data)
    {
        if (empty($data)) {
            dd("您上傳的json檔案內容我問題");
        }

        foreach ($data as $value) {
            $result = AirPollution::where('publish_time', $value['PublishTime'])
                          ->where('sitename', $value['SiteName'])
                          ->get();
            if (!($result->isEmpty())) {
                $this->store($value);
            } 
            // else {
            //     dd(AirPollution::where('publish_time', '=', $value['PublishTime'])->get());
            // }
        }
    }

    /**
     * Store one data.
     *
     * @param array $data
     */
    public function store($data)
    {
        $airpollution = new AirPollution;

        $airpollution->sitename = $data['SiteName'];
        $airpollution->pm25 = $data['PM2.5'];
        $airpollution->county = $this->site_to_county[$data['SiteName']];
        $airpollution->so2 = $data['SO2'];
        $airpollution->co = $data['CO'];
        $airpollution->o3 = $data['O3'];
        $airpollution->pm10 = $data['PM10'];
        $airpollution->no2 = $data['NO2'];
        $airpollution->wind_speed = $data['WIND_SPEED'];
        $airpollution->wind_direction = $data['WIND_DIREC'];
        $airpollution->publish_time = str_replace('/', '-', $data['PublishTime']);
        $airpollution->date = substr($data['PublishTime'],0,10);
        $airpollution->temp = $data['AMB_TEMP'];

        $airpollution->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
