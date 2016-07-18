<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\AirPollution;

class UploadFilesController extends Controller
{
    /**
     * The original name of uploaded file.
     *
     * @var string
     */
    private $file_name;

    /**
     * The original content of uploaded file.
     * 
     * @var string
     */
    private $file_content;

    /**
     * Record publish_time if sitename && publish_time find in DB. 
     *
     * @var array
     */
    private $existed = array();

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
            $this->file_name = $file->getClientOriginalName(); // file name
            $this->file_content = file_get_contents($file->getRealPath());
            $data = json_decode($this->file_content, true);
            $this->check($data);
            $this->store($data);
            $request->session()->flash('alert-success', '成功上傳 '.$this->file_name.' 資料');
            return redirect()->route('file-upload.index');  
        } else {
            $request->session()->flash('alert-danger', '檔案上傳失敗');
            return redirect()->route('file-upload.index');
        }
    }

    /**
     * 查看資料是否重複.
     *
     * @param array $data
     */
    public function check($data)
    {
        if (empty($data) || $data == null) {
            $request->session()->flash('alert-danger', '喔不～您上傳的json檔案格式有問題');
            return redirect()->route('file-upload.index');
        } 

        $site = $data[0]['SiteName'];
        $p_t = array();

        foreach ($data as $value) {
            if ($this->hasIndex($value['PublishTime']) == "") {
                $request->session()->flash('alert-error', '喔不～您上傳的json檔案內容有問題');
                return redirect()->route('file-upload.index');
            }
            array_push($p_t, $value['PublishTime']);
        }

        $result = AirPollution::select('publish_time')->where('sitename', $site)->whereIn('publish_time', $p_t)->get()->toArray();
        
        array_push($this->existed, '*'); // set index 0 to *
        foreach ($result as $key => $value) {
            array_push($this->existed, $value['publish_time']);
        }
    }

    /**
     * Store one data.
     *
     * @param array $data
     */
    public function store($data)
    {
        $ready_insert = array();

        $timer = 500;
        foreach ($data as $value) {
            if (array_search($value['PublishTime'], $this->existed) == false) {
                $temp = array(
                    'sitename' => $this->hasIndex($value['SiteName']),
                    'pm25' => $this->hasIndex($value['PM25']),
                    'county' => $this->site_to_county[$value['SiteName']],
                    'so2' => $this->hasIndex($value['SO2']),
                    'co' => $this->hasIndex($value['CO']),
                    'o3' => $this->hasIndex($value['O3']),
                    'pm10' => $this->hasIndex($value['PM10']),
                    'no2' => $this->hasIndex($value['NO2']),
                    'wind_speed' => $this->hasIndex($value['WIND_SPEED']),
                    'wind_direction' => $this->hasIndex($value['WIND_DIREC']),
                    'publish_time' => str_replace('/', '-', $value['PublishTime']),
                    'date' => substr($value['PublishTime'],0,10),
                    'temp' => $this->hasIndex($value['AMB_TEMP']),);
                array_push($ready_insert, $temp);
            }
            if ($timer-- == 0) {
                $airpollution = new AirPollution;

                $airpollution->insert($ready_insert);

                $ready_insert = array();
                $timer = 500;
            }
        }

        if ($timer != 500) {
            $airpollution = new AirPollution;

            $airpollution->insert($ready_insert);
        }
    }

    /**
     * Check out array whether has this index.
     *
     * @param string $value
     * @return $value|empty string
     */
    public function hasIndex($value)
    {
        return isset($value) ? $value : "";
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
