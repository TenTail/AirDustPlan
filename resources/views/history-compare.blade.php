@extends("layouts.master")

@section("title", "空塵計")

@section('head-javascript')
<script src="{{ asset('highstock/js/highstock.js') }}"></script>
@stop

@section("content")

<div class="col-md-3">
    <h2>選擇年份</h2>
    <div class="col-md-6">
        <select id="year" class="form-control">
            @for ($i = 2015; $i < 2017; $i++)
                <option value="{{ $i }}">{{ $i }}年</option>
            @endfor
        </select>
    </div>
    <div class="col-md-6">
        <select id="month" class="form-control">
            @for ($i = 1; $i < 13; $i++)
                <option value="{{ sprintf('%02d', $i) }}">{{ $i }}月</option>
            @endfor
        </select>
    </div>
    <h2>選擇縣市</h2>
    <select id="county" class="form-control">
        <?php $county = ['新北市', '屏東縣', '臺南市', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中市', '雲林縣', '高雄市', '臺北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園市', '彰化縣', '花蓮縣', '南投縣'];?>
        @for ($i = 0, $length = count($county); $i < $length; $i++)
            <option value="{{ $county[$i] }}">{{ $county[$i] }}</option>
        @endfor
    </select>
    <h2>選擇測站</h2>
    <select id="sitename" class="form-control">
        
    </select>
</div>
<div class="col-md-9">
    
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
    ];
    
    function searchSiteIndex(county) {
        for(var i = 0, length1 = all_site.length; i < length1; i++){
            if (all_site[i].county == county) {
                return i;
            }
        }
    }

    function loadSite() {
        var index = searchSiteIndex($('#county').val());
        $('#sitename').empty(); // 清空
        // 加入新的<option>
        for(var i = 0, length1 = all_site[index].sitename.length; i < length1; i++){
            var option = $('<option></option>').attr('value', all_site[index].sitename[i]).text(all_site[index].sitename[i]);
            $('#sitename').append(option);
        }
    }

    $('#county').change(function () {
        loadSite();
    });

    $(document).ready(function () {
        loadSite();
    });
</script>
@stop