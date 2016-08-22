@extends('layouts.master')

@section('title', '空塵計')

@section('head-javascript')
<script src="{{ asset('highcharts/js/highcharts.js') }}"></script>
<script src="{{ asset('highcharts/js/themes/grid-light.js') }}"></script>
@endsection

@section('content')
<style>
    .sitename {
        text-align: center;
    }
    .chart {
        width: 100%;
        height: 400px;
        padding-bottom: 50px;
    }
</style>

<div class="col-md-12">
    <button class="btn btn-success" style="margin: 20px 0;font-size: 16pt;" onClick="changeChartMode()">更改樣式</button>
    <h2 class="sitename">斗六</h2>
    <div class="chart" id="斗六"></div>
    <button class="btn btn-success" style="margin: 20px 0;font-size: 16pt;" onClick="changeChartMode()">更改樣式</button>
    <h2 class="sitename">宜蘭</h2>
    <div class="chart" id="宜蘭"></div>
    <button class="btn btn-success" style="margin: 20px 0;font-size: 16pt;" onClick="changeChartMode()">更改樣式</button>
    <h2 class="sitename">萬里</h2>
    <div class="chart" id="萬里"></div>
    <button class="btn btn-success" style="margin: 20px 0;font-size: 16pt;" onClick="changeChartMode()">更改樣式</button>
    <h2 class="sitename">淡水</h2>
    <div class="chart" id="淡水"></div>
</div>

@endsection

@section('page-javascript')
<script>
    var chart = {
        type: 'column'
    };
    var title = {
        text: ''
    };
    var xAxis = {
        type: 'category',
        labels: {
            style: {
                fontSize: '13px',
                // fontFamily: 'Verdana, sans-serif'
            }
        },
        categories: ['一月', '二月', '三月', '四月', '五月', '六月',
                '七月', '八月', '九月', '十月', '十一月', '十二月']
    };
    var yAxis = {
        max: 80,
        min: 0,
        title: {
            text: 'PM2.5濃度平均值'
        },
        labels: {
            useHTML: true,
            formatter: function () {
                return this.value + 'μg/m<sup>3</sup>';
            },
        },
    };
    var legend = {
        enabled: true
    };

    $(function () {
        createChart();
    });

    function changeChartMode() {
        chart.type = (chart.type == 'column') ?  'line' : 'column';
        createChart();
    }

    function createChart() {
        @for ($site = ['斗六','宜蘭','萬里','淡水'], $s = 0; $s < count($site); $s++)
            {!! "$('#$site[$s]').highcharts({
                    chart: chart,
                    title: title,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    legend: legend,
                    series: [" !!}
            @for ($i = 2010; $i <= 2015; $i++)
                {!! "{name: '$i',data: ".json_encode($r_avg_data[$site[$s]][$i])."}," !!}
            @endfor
            {!! "   ]
                });" !!}
        @endfor
    };
</script>
@endsection