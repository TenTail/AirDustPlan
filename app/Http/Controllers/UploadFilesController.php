<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Models\AirPollution;

use \Exception;

use File;

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
     * Temp array to save one row history data.
     *
     * @var array
     */
    private $db_column = array(
        'sitename' => '',
        'pm25' => '',
        'county' => '',
        'so2' => '',
        'co' => '',
        'o3' => '',
        'pm10' => '',
        'no2' => '',
        'wind_speed' => '',
        'wind_direction' => '',
        'publish_time' => '',
        'temp' => '',
    );

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
        '斗六'=>'雲林縣','崙背'=>'雲林縣','臺西'=>'雲林縣','麥寮'=>'雲林縣','台西'=>'雲林縣',
        '新港'=>'嘉義縣','朴子'=>'嘉義縣',
        '新營'=>'臺南市','善化'=>'臺南市','安南'=>'臺南市','臺南'=>'臺南市','台南'=>'臺南市',
        '屏東'=>'屏東縣','潮州'=>'屏東縣','恆春'=>'屏東縣',
        '臺東'=>'臺東縣','關山'=>'臺東縣','台東'=>'臺東縣',
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
     * 資料夾批次作業
     * 請放在/public/history-files/
     */
    public function batch()
    {
        $files = File::files(public_path().'/history-files');
        foreach ($files as $key => $file) {
            $this->file_content = file_get_contents($file);
            $data = json_decode($this->file_content, true);
            $this->check($data);
            try {
                $this->store($data);
                echo "<p><span style='color: green'>成功上傳</span>".$file."</p>";
                File::delete($file);
                echo "<p style='color: blue'>已刪除".$file."</p>";
            } catch (Exception $e) {
                echo "<p><span style='color: red'>上傳失敗</span>".$file."</p>";
            }
        }
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
                // 改成foreach($value as $key => $value)
                foreach ($value as $key => $v) {
                    switch ($key) {
                        case 'SiteName':
                            if ($v == '台西') {
                                $this->db_column['sitename'] = '臺西';
                            } elseif ($v == '台東') {
                                $this->db_column['sitename'] = '臺東';
                            } elseif ($v == '台南') {
                                $this->db_column['sitename'] = '臺南';
                            } else {
                                $this->db_column['sitename'] = $v;
                            }
                            $this->db_column['county'] = $this->site_to_county[$this->db_column['sitename']];
                            break;
                        case 'PM25';
                            $this->db_column['pm25'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'SO2';
                            $this->db_column['so2'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'CO';
                            $this->db_column['co'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'O3';
                            $this->db_column['o3'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'PM10';
                            $this->db_column['pm10'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'NO2';
                            $this->db_column['no2'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'WIND_SPEED':
                            $this->db_column['wind_speed'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'WIND_DIREC':
                            $this->db_column['wind_direction'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                        case 'PublishTime':
                            $this->db_column['publish_time'] = $v;
                            break;
                        case 'AMB_TEMP':
                            $this->db_column['temp'] = $this->matchValue("/^\d+(\.\d+)?$/", $v);
                            break;
                    }
                }
                array_push($ready_insert, $this->db_column);
                $this->db_column = array(
                    'sitename' => '',
                    'pm25' => '',
                    'county' => '',
                    'so2' => '',
                    'co' => '',
                    'o3' => '',
                    'pm10' => '',
                    'no2' => '',
                    'wind_speed' => '',
                    'wind_direction' => '',
                    'publish_time' => '',
                    'temp' => '',
                );
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
     * 回傳符合 $pattern 的 $value 否則回傳 ""
     * 
     * @param string $pattern 符合的條件
     * @param int|double|string $value 要判斷的數值
     * @return ""|$value
     */
    public function matchValue($pattern, $value)
    {
        return preg_match($pattern, $value) ? $value : "";
    }
}
