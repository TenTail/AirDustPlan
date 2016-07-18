@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-javascript")
<script src="{{ asset('highmaps/js/highmaps.js') }}"></script>
<script src="https://code.highcharts.com/mapdata/countries/tw/tw-all.js"></script>
@endsection

@section("title", "空塵計")

@section("content")

<h1>空氣品質汙染指標 Open Data 下載</h1>
<h3>因為政府所提供的歷年空氣品質資料十分難以分析且格式錯亂，所以提供可自訂所需欄位的方式輸出已處理過後的資料。</h3>

{!! Form::open(array('route' => 'excel-export.export', 'method' => 'post')) !!}
<div class="col-md-6">
    <h2>選擇年份</h2>
    <select id="year" class="form-control" style="width: 50%">
        @for ($i = 2016; $i != 2013; $i--)
            <option value="{{ $i }}">{{ $i."年" }}</option>
        @endfor
    </select>
    <select id="month" class="form-control" style="width: 50%">
        @for ($i = 1; $i < 13; $i++)
            <option value="{{ sprintf('%02d', $i) }}">{{ $i."月" }}</option>
        @endfor
    </select>
    <h2>選擇欄位</h2>
    <input type="checkbox" id="pm25" name="output_data[]" value="pm25" checked>
    <label for="pm25">PM2.5</label>
    <input type="checkbox" id="pm10" name="output_data[]" value="pm10">
    <label for="pm10">PM10</label>
    <input type="checkbox" id="so2" name="output_data[]" value="so2">
    <label for="so2">SO2</label>
    <input type="checkbox" id="co" name="output_data[]" value="co">
    <label for="co">CO</label>
    <input type="checkbox" id="no2" name="output_data[]" value="no2">
    <label for="no2">NO2</label>
    <input type="checkbox" id="windspeed" name="output_data[]" value="wind">
    <label for="wind">風速和風向</label>
    <h2>選擇區域</h2>
    <input type="text" name="county" id="county" value="">
    <input type="submit" class="btn btn-success" value="下載">
</div>
<div id="map_tw" class="col-md-6" style="height: 500px; min-width: 310px; max-width: 480px; margin: 0 auto"></div>

{!! Form::close() !!}

@endsection

@section("page-javascript")

<script>
$(function () {
    // Instanciate the map
    $('#map_tw').highcharts('Map', {
        title : {
            text : 'Taiwan'
        },

        legend: {
            enabled: false
        },

        plotOptions: {
            map: {
                allAreas: false,
                joinBy: ['woe-name', 'code'],
                dataLabels: {
                    enabled: true,
                    color: '#FFFFFF',
                    format: null,
                    style: {
                        fontWeight: 'bold'
                    }
                },
                mapData: Highcharts.maps['countries/tw/tw-all'],
                tooltip: {
                    headerFormat: '',
                    pointFormatter: function () {
                        var c_en = ['Pingtung County', 'Tainan City', 'Yilan County', 'Chiayi County', 'Taitung County', 'Penghu County', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin County', 'Kaohsiung City', 'Taipei County', 'Hsinchu City', 'Hsinchu County', 'Keelung City', 'Miaoli County', 'Taoyuan County', 'Changhua County', 'Hualien County', 'Nantou County'];
                        var c_tw = ['屏東線', '臺南市', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中市', '雲林縣', '高雄市', '臺北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園市', '彰化縣', '花蓮縣', '南投縣'];

                        return c_tw[c_en.indexOf(this.code)];
                    }
                },
                allowPointSelect: true,
                states: {
                    select: {
                        color: '#FF0000'
                    },
                    hover: {
                        color: '#FF0000'
                    }
                },
                events: {
                    click: function (e) {
                        $('#county').val(countyTranslate(event.point.name));
                        // excelDownload(event.point.name);
                    }
                }
            }
            
        },

        series : [{
            //'Pingtung County', 'Tainan City', 'Yilan County', 'Chiayi County', 'Taitung County', 'Penghu County', 'Kinmen', 'Lienchiang', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin County', 'Kaohsiung City', 'Taipei County', 'Hsinchu City', 'Hsinchu County', 'Keelung City', 'Miaoli County', 'Taoyuan County', 'Changhua County', 'Hualien County', 'Nantou County'
            data: $.map(['Pingtung County', 'Tainan City', 'Yilan County', 'Chiayi County', 'Taitung County', 'Penghu County', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin County', 'Kaohsiung City', 'Taipei County', 'Hsinchu City', 'Hsinchu County', 'Keelung City', 'Miaoli County', 'Taoyuan County', 'Changhua County', 'Hualien County', 'Nantou County'], function (code) {
                return { code: code };
            })
        }]
    });
    $('svg > text[text-anchor=end]').css('display', 'none');
});

function excelDownload(county) {
    var data_string = {};
    data_string._token = $('meta[name=csrf-token]').attr('content');
    data_string.year = $('select[name=year]').val();
    data_string.output_data = $('input:checkbox').map(function() { 
        if ($(this).prop('checked')) return $(this).val(); 
    }).get();
    data_string.county = countyTranslate(county);

    $.ajax({
        type: 'POST',
        url: '{{ route('excel-export.export') }}',
        data: data_string,
        success: function (data) {
            console.log(data);
            alert('完成下載!');
        },
        error: function () {
            alert('Oops 發生錯誤...');
        }
    });
}

function countyTranslate (county) {
    var c_en = ['Pingtung', 'Tainan City', 'Yilan', 'Chiayi', 'Taitung', 'Penghu', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin', 'Kaohsiung City', 'Taipei', 'Hsinchu City', 'Hsinchu', 'Keelung City', 'Miaoli', 'Taoyuan', 'Changhua', 'Hualien', 'Nantou'];
    var c_tw = ['屏東線', '臺南市', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中市', '雲林縣', '高雄市', '臺北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園市', '彰化縣', '花蓮縣', '南投縣'];

    return c_tw[c_en.indexOf(county)];
}
</script>

@endsection
