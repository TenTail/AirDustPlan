@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section('head-css')
<style>
.fixedTable {
    margin-top: 1em;
}
.fixedTable .table {
  background-color: white;
  width: auto;
}
.fixedTable .table tr td,
.fixedTable .table tr th {
  min-width: 100px;
  width: 100px;
  min-height: 20px;
  height: 20px;
  padding: 5px;
}
.fixedTable-header {
  /*width: 510px;*/
  width: 100%;
  height: 30px;
  /*margin-left: 110px;*/
  overflow: hidden;
  border-bottom: 1px solid #CCC;
}
.fixedTable-sidebar {
  width: 110px;
  height: 310px;
  float: left;
  overflow: hidden;
  border-right: 1px solid #CCC;
}
.fixedTable-body {
  overflow: auto;
  /*width: 510px;*/
  width: 100%;
  height: 310px;
  float: left;
}
</style>
@stop

@section("head-javascript")
<script src="{{ asset('highmaps/js/highmaps.js') }}"></script>
<script src="https://code.highcharts.com/mapdata/countries/tw/tw-all.js"></script>
@endsection

@section("title", "空塵計")

@section("content")

<h1>空氣品質汙染指標 Open Data 下載</h1>
<h3>因為政府所提供的歷年空氣品質資料十分難以分析且格式錯亂，所以提供可自訂所需欄位的方式輸出已處理過後的資料。</h3>

{!! Form::open(array('route' => 'excel-export.export', 'method' => 'post')) !!}
<div class="col-md-12">
    <div class="col-md-4">
        <h2 style="text-align: center;">選擇年份</h2>
        <select name="year" id="year" class="form-control" style="width: 100%">
            @for ($i = getdate()['year']; $i != 1984; $i--)
                <option value="{{ $i }}">{{ $i."年" }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-4">
        <h2 style="text-align: center;">選擇縣市</h2>
        <select id="county" class="form-control">
            <?php $county = ['新北市', '屏東縣', '臺南市', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中市', '雲林縣', '高雄市', '臺北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園市', '彰化縣', '花蓮縣', '南投縣'];?>
            @for ($i = 0, $length = count($county); $i < $length; $i++)
                <option value="{{ $county[$i] }}">{{ $county[$i] }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-4">
        <h2 style="text-align: center;">選擇測站</h2>
        <select name="sitename" id="sitename" class="form-control">
            {{-- option --}}
        </select>
    </div>
    <div class="col-md-12" style="margin-top: 1em;">
        <input type="submit" class="btn btn-success" value="下載" >
    </div>
</div>
{!! Form::close() !!}

<div id="demo-msg" class="col-md-12" style="text-align: center;display: none;height: 340px">
    <div class="flash-message">
        <h2 class="alert alert-warning">未找到資料，請聯絡管理員。<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></h2>
    </div> <!-- end .flash-message -->
</div>

<div id="demo-table" class="col-md-12 fixedTable" style="display: none;">
    <h2 style="text-align: center;">資料預覽</h2>
    <div style="text-align: center;">
        <ul id="pagination" class="pagination pagination-lg">
            <li class="active"><span href="#" onclick="page(1)">1月</span></li>
            <li><span href="#" onclick="page(2)">2月</span></li>
            <li><span href="#" onclick="page(3)">3月</span></li>
            <li><span href="#" onclick="page(4)">4月</span></li>
            <li><span href="#" onclick="page(5)">5月</span></li>
            <li><span href="#" onclick="page(6)">6月</span></li>
            <li><span href="#" onclick="page(7)">7月</span></li>
            <li><span href="#" onclick="page(8)">8月</span></li>
            <li><span href="#" onclick="page(9)">9月</span></li>
            <li><span href="#" onclick="page(10)">10月</span></li>
            <li><span href="#" onclick="page(11)">11月</span></li>
            <li><span href="#" onclick="page(12)">12月</span></li>
        </ul>
    </div>
    <header class="fixedTable-header">
        <table class="table table-bordered">
            <thead>
                {{-- table head --}}
            </thead>
        </table>
    </header>
    <div class="fixedTable-body">
        <table class="table table-bordered">
            <tbody>
                {{-- table body --}}
            </tbody>
        </table>
    </div>
</div>

<div id="loading" style="position: fixed;top:0;left:0;background: rgba(0,0,0,0.3);width: 100%;height: 100%">
    <h1 style="position: fixed;top:50%;left: 40%;font-size: 8em;font-weight: bolder;">載入中...</h1>
</div>

@endsection

@section("page-javascript")

<script>
var data_str;
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
];

// search county then return index
function searchSiteIndex(county) {
    for(var i = 0, length1 = all_site.length; i < length1; i++){
        if (all_site[i].county == county) {
            return i;
        }
    }
}

// change sitename option
function loadSite() {
    var index = searchSiteIndex($('#county').val());
    $('#sitename').empty(); // 清空
    // 加入新的<option>
    for(var i = 0, length1 = all_site[index].sitename.length; i < length1; i++){
        var option = $('<option></option>').attr('value', all_site[index].sitename[i]).text(all_site[index].sitename[i]);
        $('#sitename').append(option);
    }
}

// update table
function updateTable(data, p = 1) {
    $table_head = $('#demo-table > header > table > thead')
    $table_body = $('#demo-table > .fixedTable-body > table > tbody')
    $table_head.empty()
    $table_body.empty()
    $table_head.append(data.keys_str)
    $table_body.append(data.data_str[p-1])

}

// ajax for table
function loadTable() {
    var post_data = {
        _token: $('meta[name=csrf-token]').attr('content'),
        year: $('#year').val(),
        sitename: $('#sitename').val(),
    }
    $.ajax({
        type: 'POST',
        url: '{{ route('excel-export.table') }}',
        data: post_data,
        success: function (data) {
            $('#loading').css('display', 'none')
            if (data == '檔案不存在') {
                $('#demo-msg').css('display', 'block')
                $('#demo-table').css('display', 'none')
            } else {
                $('#demo-msg').css('display', 'none')
                $('#demo-table').css('display', 'block')
                updateTable(data)
                data_str = data.data_str
                console.log(data_str)
            }
        },
        error: function () {
            $('#loading').css('display', 'none')
            $('#demo-msg').css('display', 'block')
            $('#demo-table').css('display', 'none')
            console.log('fail')
        }
    })
}

function page(p = 1) {
    $table_body = $('#demo-table > .fixedTable-body > table > tbody')
    $table_body.empty()
    $table_body.append(data_str[p-1])
    $('#pagination > li').removeClass('active')
    $($('#pagination > li')[p-1]).addClass('active')
}

$('#year').change(function () {
    $('#loading').css('display', 'block')
    loadTable()
})

// change sitename when county selected
$('#county').change(function () {
    $('#loading').css('display', 'block')
    loadSite()
    loadTable()
});

// change sitename when county selected
$('#sitename').change(function () {
    $('#loading').css('display', 'block')
    loadTable()
});

$(document).ready(function () {
    $('#loading').css('display', 'block')
    loadSite()
    loadTable()
});

// table
(function () {
    var demo, fixedTable;
    fixedTable = function (el) {
        var $body, $header, $sidebar;
        $body = $(el).find('.fixedTable-body');
        $sidebar = $(el).find('.fixedTable-sidebar table');
        $header = $(el).find('.fixedTable-header table');
        return $($body).scroll(function () {
            $($sidebar).css('margin-top', -$($body).scrollTop());
            return $($header).css('margin-left', -$($body).scrollLeft());
        });
    };
    demo = new fixedTable($('#demo-table'));
}.call(this));

</script>

@endsection
