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

<div class="col-md-3">
    <h2>選擇年份</h2>
    <select id="year" class="form-control">
        @for ($i = 2016; $i != 2013; $i--)
            <option value="{{ $i }}">{{ $i."年" }}</option>
        @endfor
    </select>
    <select id="month" class="form-control">
        @for ($i = 1; $i < 13; $i++)
            <option value="{{ sprintf('%02d', $i) }}">{{ $i."月" }}</option>
        @endfor
    </select>
    <h2>選擇縣市</h2>
    <select id="county" class="form-control">
        <?php $county = ['新北市', '屏東縣', '臺南市', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中市', '雲林縣', '高雄市', '臺北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園市', '彰化縣', '花蓮縣', '南投縣'];?>
        @for ($i = 0, $length = count($county); $i < $length; $i++)
            <option value="{{ $county[$i] }}">{{ $county[$i] }}</option>
        @endfor
    </select>
    <h2>選擇測站</h2>
    <select id="sitename" class="form-control">
        {{-- option --}}
    </select>
    <button id="stock" class="btn btn-success" style="margin: 20px 0;width: 100%;font-size: 24pt">繪製圖表</button>
    <button class="btn btn-warning" style="margin: 20px 0;width: 100%;font-size: 24pt" onclick="svg_to_png()">下載圖表</button>
</div>
<div class="col-md-9">
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

    // change sitename when county selected
    $('#county').change(function () {
        loadSite();
    });

    function createStock(data) {
        $('#container').highcharts('StockChart', {
            title: {
                text: 'PM2.5濃度年度比較圖:'+$('#sitename').val()+'測站',
                align: 'center',
                style: {
                    color: '#000000',
                    fontWeight: 'bold',
                },
                y: 10
            },
            rangeSelector: {
                allButtonsEnabled: true,
                buttons: [{
                    type: 'day',
                    count: 1,
                    text: '天',
                },{
                    type: 'week',
                    count: 1,
                    text: '週'
                },{
                    type: 'all',
                    text: '月'
                }],
                selected: 2,
                inputDateFormat: '%m月%d日',
                inputEditDateFormat: '%m月%d日'
            },
            navigator : {
                xAxis: {
                    dateTimeLabelFormats: {
                        week: '%d日'
                    }
                }
            },
            legend: {
                enabled: true,
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'middle',
                floating: true,
                y: -80
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%m/%d',
                    week: '%m月%e日',
                }
            },
            yAxis: {
                title: {
                    text: "PM2.5濃度"
                },
                labels: {
                    useHTML: true,
                    formatter: function () {
                        return isNaN(this.value) ? 12 : this.value + 'μg/m<sup>3</sup>';
                    },
                },
                min: 0,
                max: 120,
                plotLines: [{
                    value: 0,
                    width: 2,
                    color: 'silver'
                }]
            },
            tooltip: {
                useHTML: true,
                formatter: function () {
                    var s = '<b style="font-size: 14pt; color: #000000;">' + Highcharts.dateFormat('%m月%d日 %H:%M', this.x) + '</b>';

                    $.each(this.points, function () {
                        s += '<br/>' + '<span style="color:'+this.point.color+'">\u25CF</span>' + this.series.name + ' : ';
                        s += (this.y == 0) ? '沒有資料' : this.y + ' μg/m<sup>3</sup>';
                    });

                    return s;
                }
            },
            series: [{
                name: $('#year').val()+'年'+$('#month').val()+'月',
                data: data[0]
            },{
                name: parseInt($('#year').val()-1)+'年'+$('#month').val()+'月',
                data: data[1]
            }]
        });
        $('.highcharts-range-selector-buttons > text').text('範圍：').css(['color', '#000000','font-weight','normal']);
        $($('.highcharts-input-group > g > text')[0]).text('');
        $($('.highcharts-input-group > g > text')[2]).text('至');
        $('svg > text[text-anchor=end]').css('display', 'none');
        $('svg').attr('id', 'svg_compare');
    }

    // AQI圖表
    function createStockAQI(data) {
        $('#containerAQI').highcharts('StockChart', {
            title: {
                text: 'PM2.5 AQI指標年度比較圖:'+$('#sitename').val()+'測站',
                align: 'center',
                style: {
                    color: '#000000',
                    fontWeight: 'bold',
                },
                y: 10
            },
            rangeSelector: {
                allButtonsEnabled: true,
                buttons: [{
                    type: 'day',
                    count: 1,
                    text: '天',
                },{
                    type: 'week',
                    count: 1,
                    text: '週'
                },{
                    type: 'all',
                    text: '月'
                }],
                selected: 2,
                inputDateFormat: '%m月%d日',
                inputEditDateFormat: '%m月%d日'
            },
            navigator : {
                xAxis: {
                    dateTimeLabelFormats: {
                        week: '%d日'
                    }
                }
            },
            legend: {
                enabled: true,
                layout: 'vertical',
                align: 'left',
                verticalAlign: 'middle',
                floating: true,
                y: -80
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%m/%d',
                    week: '%m月%e日',
                }
            },
            yAxis: {
                title: {
                    text: "PM2.5 AQI指標"
                },
                labels: {
                    useHTML: true,
                    formatter: function () {
                        return isNaN(this.value) ? 12 : this.value;
                    },
                },
                min: 0,
                max: 500,
                plotLines: [{
                    value: 0,
                    width: 2,
                    color: 'silver'
                }],
                plotBands: [{
                    from: 0, to: 50, color: 'rgba(00, 255, 0, 0.5)'
                },{
                    from: 50, to: 100, color: 'rgba(255, 255, 0, 0.5)'
                },{
                    from: 100, to: 150, color: 'rgba(255, 150, 00, 0.5)'
                },{
                    from: 150, to: 200, color: 'rgba(255, 00, 00, 0.5)'
                },{
                    from: 200, to: 300, color: 'rgba(255, 00, 255, 0.5)'
                },{
                    from: 300, to: 500, color: 'rgba(85, 00, 00, 0.5)'
                },]
            },
            tooltip: {
                useHTML: true,
                formatter: function () {
                    var s = '<b style="font-size: 14pt; color: #000000;">' + Highcharts.dateFormat('%m月%d日 %H:%M', this.x) + '</b>';

                    $.each(this.points, function () {
                        s += '<br/>' + '<span style="color:'+this.point.color+'">\u25CF</span>' + this.series.name + ' : ';
                        s += (this.y == 0) ? '沒有資料' : this.y;
                    });

                    return s;
                }
            },
            series: [{
                name: $('#year').val()+'年'+$('#month').val()+'月',
                data: data[2],
                color: 'rgb(0, 0, 255)'
            },{
                name: parseInt($('#year').val()-1)+'年'+$('#month').val()+'月',
                data: data[3],
                color: 'rgb(0, 0, 0)'
            }]
        });
        $('.highcharts-range-selector-buttons > text').text('範圍：').css(['color', '#000000','font-weight','normal']);
        $($('.highcharts-input-group > g > text')[0]).text('');
        $($('.highcharts-input-group > g > text')[2]).text('至');
        $('svg > text[text-anchor=end]').css('display', 'none');
        $('svg').attr('id', 'svg_compare');
    }

    // ajax post 
    function stockPost()
    {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') }
        });

        var post_data = {
            _token: $('meta[name=csrf-token]').attr('content'),
            year: $('#year').val(),
            month: $('#month').val(),
            sitename: $('#sitename').val(),
        };

        $.ajax({
            type: 'POST',
            url: '{{ route('history-compare.compare') }}',
            data: post_data,
            success: function (data) {
                // console.log(data);
                createStock(data);
                createStockAQI(data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("查無資料");
                console.log(jqXHR, textStatus);
            }
        });
    }

    // stock button click
    $('#stock').click(function () {
        stockPost();
    });

    // 網頁第一次載入
    $(document).ready(function () {
        loadSite();
        stockPost();
    });

    // stock export png
    function svg_to_png() {
        // the button handler
        var chart = $('#container').highcharts();
        chart.exportChart(null, {
            
        });
    }
</script>
@stop