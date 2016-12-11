@extends("layouts.master")

@section('csrf-token')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@stop

@section("title", "空塵計")

@section('head-javascript')
<script src="{{ asset('highstock/js/highstock.js') }}"></script>
<script src="{{ asset('highstock/js/modules/exporting.js') }}"></script>
<script src="{{ asset('highstock/js/themes/grid-light.js') }}"></script>
@stop

@section("content")

<div class="col-md-12">
    <div class="col-md-4">
        <h1 style="text-align: center;">圖表設定</h1>
        <div class="col-md-12">
            <h2>時間　：
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#checkDate">
                選擇年份
                </button>
            </h2>

            <!-- Modal -->
            <div class="modal fade" id="checkDate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">選擇年份</h4>
                        </div>
                        <div class="modal-body" style="height: {{ ceil((getdate()['year']-1984)/4+1)*25+65 }}px;">
                            <h2 style="text-align: center;">選擇年份</h2>
                            @for ($i = getdate()['year']; $i != 1984; $i--)
                            <div style="width: 25%;float: left;text-align: center;">
                                <input type="checkbox" class="input-date" id="{{ $i }}" value="{{ $i }}" onchange="setData()">
                                <label for="{{ $i }}" style="padding-right: 10px;">{{ $i."年" }}</label>
                            </div>
                            @endfor
                        </div>
                        <div class="modal-body" style="height: 190px;">
                            <h2 style="text-align: center;">選擇月份</h2>
                            @for ($i = 1; $i != 12; $i++)
                            <div style="width: 33.33%;float: left;text-align: center;">
                                <input type="radio" name="month" id="{{ $i }}" value="{{ $i }}" onchange="setData()">
                                <label for="{{ $i }}" style="padding-right: 10px;">{{ $i."月~".($i+1)."月" }}</label>
                            </div>
                            @endfor
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-warning" onclick="reset('date');setData()">清除</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="setData()">確定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <h2>測站　：
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#selectSite">
                選擇測站
                </button>
            </h2>

            <!-- Modal -->
            <div class="modal fade" id="selectSite" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">選擇測站</h4>
                        </div>
                        <div class="modal-body" style="height: {{ ceil((getdate()['year']-1984)/4+1)*25 }}px;">
                            <h2 style="text-align: center;">選擇縣市</h2>
                            <select id="county" class="form-control" onchange="loadSite();setData();">
                                <?php $county = ['新北市', '屏東縣', '臺南市', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中市', '雲林縣', '高雄市', '臺北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園市', '彰化縣', '花蓮縣', '南投縣'];?>
                                @for ($i = 0, $length = count($county); $i < $length; $i++)
                                    <option value="{{ $county[$i] }}">{{ $county[$i] }}</option>
                                @endfor
                            </select>
                            <h2 style="text-align: center;">選擇測站</h2>
                            <select id="sitename" class="form-control" onchange="setData()">
                                {{-- option --}}
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="setData()">確定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <h2>汙染物：
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#selectPollution">
                選擇汙染物
                </button>
            </h2>

            <!-- Modal -->
            <div class="modal fade" id="selectPollution" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">選擇汙染物</h4>
                        </div>
                        <div class="modal-body" style="height: 50px;">
                            <?php $pollution = ['PM2.5', 'PM10', 'SO2', 'CO'] ?>
                            @for ($i = 0; $i < count($pollution); $i++)
                            <div style="width: 25%;float: left;text-align: center;">
                                <input type="checkbox" class="input-pollution" id="{{ $pollution[$i] }}" value="{{ $pollution[$i] }}" onchange="setData()">
                                <label for="{{ $pollution[$i] }}" style="padding-right: 10px;">{{ $pollution[$i] }}</label>
                            </div>
                            @endfor
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-warning" onclick="reset();setData();">清除</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close" onclick="setData()">確定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <h1 style="text-align: center;">目前狀態</h1>
        <div class="col-md-10">
            <table class="table table-bordered" style="font-size: 20px;margin-top: 20px;">
                <tr>
                    <th style="width: 80px">年份</th>
                    <td id="show-year"></td>
                </tr>
                <tr>
                    <th style="width: 80px">月份</th>
                    <td id="show-month"></td>
                </tr>
                <tr>
                    <th style="width: 80px">測站</th>
                    <td id="show-site"></td>
                </tr>
                <tr>
                    <th style="width: 80px">汙染物</th>
                    <td id="show-pollution"></td>
                </tr>
            </table>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-success btn-lg" style="margin-top: 20px;" onclick="startDraw()">繪製圖表</button>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div id="container" style="height: 400px; min-width: 100%;margin-top: 20px;"></div>
    <div id="containerAQI" style="height: 500px; min-width: 100%;margin-top: 20px;padding-top: 50px;"></div>
</div>

@endsection

@section('page-javascript')
<script>
    var all_site = [
        {county: '基隆市', sitename: ['基隆']},
        {county: '嘉義市', sitename: ['嘉義']},
        {county: '高雄市', sitename: ['美濃','大寮','橋頭','仁武','鳳山','林園','楠梓','左營','前金','前鎮','小港','復興']},
        {county: '新北市', sitename: ['汐止','萬里','新店','土城','板橋','新莊','菜寮','林口','淡水','三重','永和']},
        {county: '臺北市', sitename: ['士林','中山','萬華','古亭','松山','大同','陽明']},
        {county: '桃園市', sitename: ['桃園','大園','觀音','平鎮','龍潭','中壢']},
        {county: '新竹縣', sitename: ['湖口','竹東']},
        {county: '新竹市', sitename: ['新竹']},
        {county: '苗栗縣', sitename: ['頭份','苗栗','三義']},
        {county: '臺中市', sitename: ['豐原','沙鹿','大里','忠明','西屯']},
        {county: '彰化縣', sitename: ['彰化','線西','二林']},
        {county: '南投縣', sitename: ['南投','竹山','埔里']},
        {county: '雲林縣', sitename: ['斗六','崙背','臺西','麥寮']},
        {county: '嘉義縣', sitename: ['新港','朴子']},
        {county: '臺南市', sitename: ['新營','善化','安南','臺南']},
        {county: '屏東縣', sitename: ['屏東','潮州','恆春']},
        {county: '臺東縣', sitename: ['臺東','關山']},
        {county: '宜蘭縣', sitename: ['宜蘭','冬山']},
        {county: '花蓮縣', sitename: ['花蓮']},
        {county: '澎湖縣', sitename: ['馬公']},
        {county: '連江縣', sitename: ['馬祖']},
        {county: '金門縣', sitename: ['金門']}
    ]

    // search county then return index
    function searchSiteIndex(county) {
        for(var i = 0, length1 = all_site.length; i < length1; i++){
            if (all_site[i].county == county) {
                return i
            }
        }
    }

    // change sitename when county selected
    function loadSite() {
        var index = searchSiteIndex($('#county').val())
        $('#sitename').empty() // 清空
        // 加入新的<option>
        for(var i = 0, length1 = all_site[index].sitename.length; i < length1; i++){
            var option = $('<option></option>').attr('value', all_site[index].sitename[i]).text(all_site[index].sitename[i])
            $('#sitename').append(option)
        }
    }

    $(document).ready(function () {
        loadSite()
        setData()
    })

    // reset checkbox
    function reset(re) {
        if (re == "date") {
            $('.input-date:checkbox').map(function() {  
                if ($(this).prop('checked')) $(this).prop('checked', false);  
            }).get()
            $('input:radio').map(function() {  
                if ($(this).prop('checked')) $(this).prop('checked', false);  
            }).get()
         } else {
            $('.input-pollution:checkbox').map(function() {  
                if ($(this).prop('checked')) $(this).prop('checked', false);  
            }).get()
         }
    }

    var year, two_month, sitename, pollution;

    // set search data
    function setData() {
        // get value that selected checkbox
        year = $('.input-date:checkbox').map(function() {  
            if ($(this).prop('checked')) return $(this).val();  
        }).get()
        // get value that selected checkbox
        pollution = $('.input-pollution:checkbox').map(function() {  
            if ($(this).prop('checked')) return $(this).val();  
        }).get()
        // get value that selected radiobox
        two_month = $('input:radio').map(function() {  
            if ($(this).prop('checked')) return $(this).val();  
        }).get()
        // get sitename
        sitename = $('#sitename').val()
        showData()
    }

    // show user set data on table
    function showData() {
        $('#show-year').html(year.length == 0 ? '<span style="color: red;">尚未設定</span>' : "")
        year.forEach(function (element, index, array) {
            $('#show-year').append(element+"年,")
        })
        $('#show-month').html(two_month.length == 0 ? '<span style="color: red;">尚未設定</span>' : "")
        two_month.forEach(function (element, index, array) {
            $('#show-month').append(element+"月~"+(parseInt(element)+1)+"月")
        })
        $('#show-site').html(sitename+"站")
        $('#show-pollution').html(pollution.length == 0 ? '<span style="color: red;">尚未設定</span>' : "")
        pollution.forEach(function (element, index, array) {
            $('#show-pollution').append(element+",")
        })
    }

    // button start draw 
    function startDraw() {
        if (year.length == 0 || two_month.length == 0 || sitename == '' || pollution.length == 0 ) {
            alert('請設定時間、測站、污染物。');
        } else {
            console.log(year, two_month, sitename, pollution);
        }
    }

</script>
@stop