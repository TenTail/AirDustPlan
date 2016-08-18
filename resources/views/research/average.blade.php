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
    <h2 class="sitename">斗六</h2>
    <div class="chart" id="斗六"></div>
    <h2 class="sitename">宜蘭</h2>
    <div class="chart" id="宜蘭"></div>
    <h2 class="sitename">萬里</h2>
    <div class="chart" id="萬里"></div>
    <h2 class="sitename">淡水</h2>
    <div class="chart" id="淡水"></div>
</div>

@endsection

@section('page-javascript')
<script>
    var title = {
        text: ''
    }
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
        }
    };
    var legend = {
        enabled: true
    };

    $(function () {
        $('#斗六').highcharts({
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [
            @for ($i = 2010; $i <= 2015; $i++)
                {!! "{name: '$i',data: ".json_encode($r_avg_data['斗六'][$i])."}," !!}
            @endfor
            ]
        });
        $('#宜蘭').highcharts({
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [
            @for ($i = 2010; $i <= 2015; $i++)
                {!! "{name: '$i',data: ".json_encode($r_avg_data['宜蘭'][$i])."}," !!}
            @endfor
            ]
        });
        $('#萬里').highcharts({
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [
            @for ($i = 2010; $i <= 2015; $i++)
                {!! "{name: '$i',data: ".json_encode($r_avg_data['萬里'][$i])."}," !!}
            @endfor
            ]
        });
        $('#淡水').highcharts({
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [
            @for ($i = 2010; $i <= 2015; $i++)
                {!! "{name: '$i',data: ".json_encode($r_avg_data['淡水'][$i])."}," !!}
            @endfor
            ]
        });
    });
</script>
@endsection