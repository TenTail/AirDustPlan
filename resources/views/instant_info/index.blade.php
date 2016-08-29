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
	<table border="1" id="conty_table" style="font-size: 14px; text-align: center">
		@for($i = 0 ; $i < count($data) ; $i++)
			<td><button value="{!! $data[$i] !!}" onclick="getInstantValue(this.value)">{!! $data[$i] !!}</button></td>

			@if($i % 5 == 0 && $i != 0)
				</tr>
			@endif
			
		@endfor
	</table>
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
					                borderWidth: 0
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