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
    <div class="col-md-6">
        <h2 class="sitename">斗六</h2> 
        <div id="斗六" class="chart"></div>
    </div>
    <div class="col-md-6">
        <h2 class="sitename">宜蘭</h2> 
        <div id="宜蘭" class="chart"></div>
    </div>
    <div class="col-md-6">
        <h2 class="sitename">萬里</h2> 
        <div id="萬里" class="chart"></div>
    </div>
    <div class="col-md-6">
        <h2 class="sitename">淡水</h2> 
        <div id="淡水" class="chart"></div>
    </div>

</div>
@endsection

@section('page-javascript')
<script>
    var chart = {
        type: 'column',
    };
    var title = {
        text: ''
    }
    var xAxis = {
        type: 'category',
        labels: {
            rotation: -45,
            style: {
                fontSize: '13px',
                // fontFamily: 'Verdana, sans-serif'
            }
        }
    };
    var yAxis = {
        max: 60,
        min: 0,
        title: {
            text: 'PM2.5濃度平均值'
        }
    };
    var legend = {
        enabled: false
    };

    $(function () {
        $('#斗六').highcharts({
            chart: chart,
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [{
                name: '斗六',
                data: {!! json_encode($r_avg_data['斗六']) !!}
            }]
        });
        $('#宜蘭').highcharts({
            chart: chart,
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [{
                name: '宜蘭',
                data: {!! json_encode($r_avg_data['宜蘭']) !!}
            }]
        });
        $('#萬里').highcharts({
            chart: chart,
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [{
                name: '萬里',
                data: {!! json_encode($r_avg_data['萬里']) !!}
            }]
        });
        $('#淡水').highcharts({
            chart: chart,
            title: title,
            xAxis: xAxis,
            yAxis: yAxis,
            legend: legend,
            series: [{
                name: '淡水',
                data: {!! json_encode($r_avg_data['淡水']) !!}
            }]
        });
    });
</script>
@endsection