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
                                <input type="checkbox" class="input-year" id="{{ $i }}" value="{{ $i }}" onchange="setData()">
                                <label for="{{ $i }}" style="padding-right: 10px;">{{ $i."年" }}</label>
                            </div>
                            @endfor
                        </div>
                        <div class="modal-body" style="height: 190px;">
                            <h2 style="text-align: center;">選擇月份</h2>
                            @for ($i = 1; $i != 12; $i++)
                            <div style="width: 33.33%;float: left;text-align: center;">
                                <input type="radio" name="month" class="input-month" id="{{ $i }}" value="{{ $i }}" onchange="setData()">
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
                                <input type="radio" name="pollution" class="input-pollution" id="{{ $pollution[$i] }}" value="{{ strtolower($pollution[$i]) }}" onchange="setData()">
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

<div id="demo-msg" class="col-md-12" style="text-align: center;display: none;height: 340px">
    <div class="flash-message">
        <h2 class="alert alert-warning">未找到資料，請聯絡管理員。<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></h2>
    </div> <!-- end .flash-message -->
</div>

<div class="col-md-12">
    <div id="container" style="height: 400px; min-width: 100%;margin-top: 20px;"></div>
    <div id="containerAQI" style="height: 500px; min-width: 100%;margin-top: 20px;padding-top: 50px;"></div>
</div>

<div id="loading" style="position: fixed;top:0;left:0;background: rgba(0,0,0,0.3);width: 100%;height: 100%">
    <h1 style="position: fixed;top:50%;left: 40%;font-size: 8em;font-weight: bolder;">載入中...</h1>
</div>

@endsection

@section('page-javascript')
<script src="{{ asset('js/compare2.js') }}"></script>
<script>
    $(document).ready(function () {
        loadSite()
        loading(false)
        setData()
    })

    // button ajax to HistoryCompareController@compare2
    function startDraw() {
        if (year.length == 0 || two_month.length == 0 || sitename == '' || pollution.length == 0 ) {
            alert('請設定時間、測站、污染物。')
        } else {
            loading(true)
            $('#demo-msg').css('display', 'none')
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') }
            })
            postData = {
                _token: $('meta[name=csrf-token]').attr('content'),
                year: year,
                month: two_month[0],
                sitename: sitename,
                pollution: pollution,
            }
            $.ajax({
                type: 'POST',
                url: '{{ route('history-compare2.compare') }}',
                data: postData,
                success: function (data) {
                    if (data.length){
                        drawChart(data)
                    }
                    else {
                        $('#demo-msg').css('display', 'block')  
                        loading(false)  
                    }
                },
                error: function () {
                    $('#demo-msg').css('display', 'block')
                    loading(false)
                }
            })
        }
    }

    function setY() {
        if (pollution == "pm2.5") {
            return [{
                labels: {
                    format: '{value}μg/m3',
                    style: {
                        color: 'rgb(00,00,00)'
                    },
                    align: 'left',
                },
                title: {
                    text: 'PM2.5',
                    style: {
                        color: 'rgb(00,00,00)'
                    }
                },
            }]
        } else if (pollution == "pm10") {
            return [{
                labels: {
                    format: '{value}μg/m3',
                    style: {
                        color: 'rgb(00,00,00)'
                    },
                    align: 'left',
                },
                title: {
                    text: 'PM10',
                    style: {
                        color: 'rgb(00,00,00)'
                    }
                },
            }]
        } else {
            return [{
                labels: {
                    format: '{value}μg/m3',
                    style: {
                        color: 'rgb(00,00,00)'
                    },
                    align: 'left',
                },
                title: {
                    text: 'SO2',
                    style: {
                        color: 'rgb(00,00,00)'
                    }
                },
            }]
        }
    }
    function setCharts() {
        return {
            xAxis: [{
                type: 'datetime',
                dateTimeLabelFormats: {
                    day: '%m/%d',
                    week: '%m月%e日',
                },
                tickInterval: 24 * 60 * 60 * 1000,
            }],
            yAxis: setY(),
            rangeSelector: {
                allButtonsEnabled: true,
                buttons: [{
                    type: 'week',
                    count: 1,
                    text: '週'
                },{
                    type: 'month',
                    count: 1,
                    text: '一個月'
                },{
                    type: 'all',
                    text: '兩個月'
                }],
                selected: 2,
                buttonTheme: {
                    width: 60
                },
                inputDateFormat: '%m月%d日',
                inputEditDateFormat: '%m月%d日'
            },
            tooltip: {
                shared: true,
                useHTML: true,
                formatter: function () {
                    var s = '<b style="font-size: 14pt; color: #000000;">' + Highcharts.dateFormat('%m月%d日', this.x) + '</b>';

                    $.each(this.points, function () {
                        s += '<br/>' + '<span style="color:'+this.point.color+'">\u25CF</span>' + this.series.name + ' : ';
                        s += (this.y == 0) ? '沒有資料' : this.y + 'μg/m3';
                    });

                    return s;
                }
            },
            legend: {
                enabled: true,
                layout: 'vertical',
                align: 'left',
                x: 20,
                verticalAlign: 'top',
                y: 90,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            }
        }
            
    }

    function drawChart(data) {
        setting = setCharts()
        $('#container').highcharts('StockChart', {
            title: {
                text: sitename+'站歷史空汙比較圖'
            },
            subtitle: {
                text: two_month[0]+'月~'+(parseInt(two_month[0])+1)+'月之比較'
            },
            xAxis: setting.xAxis,
            yAxis: setting.yAxis,
            rangeSelector: setting.rangeSelector,
            tooltip: setting.tooltip,
            legend: setting.legend,
            series: data,
            credits: {
                enabled: false
            },
        })
        loading(false)
    }

</script>
@stop