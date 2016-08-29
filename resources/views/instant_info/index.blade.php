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
	<table border="1" id="county_table" align="center">
		@for($i = 0 ; $i < count($data) ; $i++)
			@if($i < 5)
				<td><button type="button" class="btn btn-primary" style="width: 100%;font-size: 20pt;" value="{!! $data[$i] !!}" onclick="getInstantValue(this.value)">{!! $data[$i] !!}</button></td>
			@endif
			@if($i > 4 && $i <10)
				<td><button type="button" class="btn btn-success" style="width: 100%;font-size: 20pt;" value="{!! $data[$i] !!}" onclick="getInstantValue(this.value)">{!! $data[$i] !!}</button></td>
			@endif
			@if($i > 9 && $i <15)
				<td><button type="button" class="btn btn-warning" style="width: 100%;font-size: 20pt;" value="{!! $data[$i] !!}" onclick="getInstantValue(this.value)">{!! $data[$i] !!}</button></td>
			@endif
			@if($i > 14 && $i <20)
				<td><button type="button" class="btn btn-info" style="width: 100%;font-size: 20pt;" value="{!! $data[$i] !!}" onclick="getInstantValue(this.value)">{!! $data[$i] !!}</button></td>
			@endif
			@if($i == 4 || $i == 9 || $i == 14)
				</tr>
			@endif
			
		@endfor
	</table>
	{{-- <table id="county_table" align="center">
		<tr>
			<td><button type="button" class="btn btn-success" style="width: 100%;font-size: 20pt; padding:0" value="臺北市
			" onclick="getInstantValue()">臺北市</button></td>
			<td><button type="button" class="btn btn-success" style="width: 100%;font-size: 20pt; padding:0" value="新北市" onclick="getInstantValue()">新北市</button></td>
			<td><button type="button" class="btn btn-success" style="width: 100%;font-size: 20pt; padding:0" value="基隆市" onclick="getInstantValue()">基隆市</button></td>
			<td><button type="button" class="btn btn-success" style="width: 100%;font-size: 20pt; padding:0" value="桃園市" onclick="getInstantValue()">桃園市</button></td>
			<td><button type="button" class="btn btn-success" style="width: 100%;font-size: 20pt; padding:0" value="新竹市" onclick="getInstantValue()">新竹市</button></td>
		</tr>
		<tr>
			<td><button style="font-size:16px" value="新竹縣" onclick="getInstantValue()">新竹縣</button></td>
			<td><button style="font-size:16px" value="苗栗縣" onclick="getInstantValue()">苗栗縣</button></td>
			<td><button style="font-size:16px" value="臺中市" onclick="getInstantValue()">臺中市</button></td>
			<td><button style="font-size:16px" value="彰化縣" onclick="getInstantValue()">彰化縣</button></td>
			<td><button style="font-size:16px" value="南投縣" onclick="getInstantValue()">南投縣</button></td>
		</tr>
			<tr>
			<td><button style="font-size:16px" value="雲林縣" onclick="getInstantValue()">雲林縣</button></td>
			<td><button style="font-size:16px" value="嘉義市" onclick="getInstantValue()">嘉義市</button></td>
			<td><button style="font-size:16px" value="嘉義縣" onclick="getInstantValue()">嘉義縣</button></td>
			<td><button style="font-size:16px" value="臺南市" onclick="getInstantValue()">臺南市</button></td>
			<td><button style="font-size:16px" value="高雄市" onclick="getInstantValue()">高雄市</button></td>
		</tr>
			<tr>
			<td><button style="font-size:16px" value="屏東縣" onclick="getInstantValue()">屏東縣</button></td>
			<td><button style="font-size:16px" value="宜蘭縣" onclick="getInstantValue()">宜蘭縣</button></td>
			<td><button style="font-size:16px" value="花蓮縣" onclick="getInstantValue()">花蓮縣</button></td>
			<td><button style="font-size:16px" value="臺東縣" onclick="getInstantValue()">臺東縣</button></td>
			<td><button style="font-size:16px" value="澎湖縣" onclick="getInstantValue()">澎湖縣</button></td>
		</tr>
	</table> --}}
	<div id="show_data">
		
	</div>
@endsection

@section("page-javascript")
<script type="text/javascript">
	function getInstantValue(county)
	{
		$.ajaxSetup({
	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }
		});
		$.ajax({
		    url: 'instant_info',
		    type: 'post',
		    data: {county: county},
		    // dataType: 'JSON',
		    success: function (data) {
		        alert("success");
		       	console.log(data);
		       	// for(var i = 0; i < data.length ; i++) {
	       		// 	$('#show_data').append('<p>測站名稱 : ' + data[i].sitename + ' 所屬縣市 : ' + data[i].county + ' PSI : ' + data[i].psi + ' 發佈時間 : ' + data[i].publish_time + '</p>');  	
	       		// };
	       		var seriesOptions = [], seriesCounter = 0;
			  	$(function () {

				    function createChart() {
				    	$('#show_data').highcharts({
					        chart: {
					            type: 'column'
					        },
					        title: {
					            text: data[0].county
					        },
					        xAxis: {
					            categories: [
					                data[0].publish_time
					            ],
					            crosshair: true
					        },
					        yAxis: {
					            min: 0,
					            title: {
					                text: 'PSI'
					            }
					        },
					        tooltip: {
					            // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					            pointFormat: '<table><tr><td style="color:{series.color}; padding:0; font-size:16px">{series.name}: </td>' +
					                '<td style="padding:0; font-size:16px"><b>{point.y:.1f}</b></td></tr>',
					            footerFormat: '</table>',
					            shared: true,
					            useHTML: true
					        },
					        plotOptions: {
					            column: {
					                pointPadding: 1,
					                borderWidth: 2
					            }
					        },
					        series: seriesOptions
					    });
				    };

				    $.each(data, function (i, name) {
				            seriesOptions[i] = {
				                name: data[i].sitename,
				                data: [parseInt(data[i].psi)]
				            };

				            // As we're loading the data asynchronously, we don't know what order it will arrive. So
				            // we keep a counter and create the chart when all the data is loaded.
				            seriesCounter += 1;

				            if (seriesCounter === data.length) {
				                createChart();
				            }  
				    });
				});
		    },
		    error: function (e) {
		    	alert("Something error!");
		    }
		});
	}	


</script>

@endsection