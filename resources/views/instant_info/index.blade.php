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
	{{-- <table id="county_table" align="center">
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
	</table> --}}
{{-- 	<div id="psi_data">
		
	</div>
	<div id="pm25_data">
		
	</div> --}}
	<div id="map" style="width: 100%; height:400px"></div>
@endsection

@section("page-javascript")
<script type="text/javascript">
	// $('#psi_data').ready(getInstantValue("雲林縣"));

	// function getInstantValue(county)
	// {
	// 	$.ajaxSetup({
	//         headers: {
	//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	//         }
	// 	});
	// 	$.ajax({
	// 		url: 'instant_info',
	// 	    type: 'post',
	// 	    data: {county: county},
	// 	    // dataType: 'JSON',
	// 	    success: function (data) {
	// 	        // alert("success");
	// 	       	console.log(data);

	// 	       	// Create a chart for PSI
	//        		var seriesOptions = [], seriesCounter = 0;
	// 		  	$(function () {

	// 			    function createPsiChart() {
	// 			    	$('#psi_data').highcharts({
	// 				        chart: {
	// 				            type: 'column'
	// 				        },
	// 				        title: {
	// 				            text: data[0].county
	// 				        },
	// 				        xAxis: {
	// 				            categories: [
	// 				                data[0].publish_time
	// 				            ],
	// 				            crosshair: true
	// 				        },
	// 				        yAxis: {
	// 				            min: 0,
	// 				            title: {
	// 				                text: 'PSI'
	// 				            }
	// 				        },
	// 				        tooltip: {
	// 				            // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
	// 				            pointFormat: '<table><tr><td style="color:{series.color}; padding:0; font-size:16px">{series.name}: </td>' +
	// 				                '<td style="padding:0; font-size:16px"><b>{point.y:.1f}</b></td></tr>',
	// 				            footerFormat: '</table>',
	// 				            shared: true,
	// 				            useHTML: true
	// 				        },
	// 				        plotOptions: {
	// 				            column: {
	// 				                pointPadding: 1,
	// 				                borderWidth: 2
	// 				            }
	// 				        },
	// 				        series: seriesOptions
	// 				    });
	// 			    };

	// 			    $.each(data, function (i, name) {
	// 			            seriesOptions[i] = {
	// 			                name: data[i].sitename,
	// 			                data: [parseInt(data[i].psi)]
	// 			            };

	// 			            // As we're loading the data asynchronously, we don't know what order it will arrive. So
	// 			            // we keep a counter and create the chart when all the data is loaded.
	// 			            seriesCounter += 1;

	// 			            if (seriesCounter === data.length) {
	// 			                createPsiChart();
	// 			            }  
	// 			    });
	// 			});

	// 			// Create a chart for PM2.5
	// 			var seriesOptions = [], seriesCounter = 0;
	// 		  	$(function () {

	// 			    function createPsiChart() {
	// 			    	$('#pm25_data').highcharts({
	// 				        chart: {
	// 				            type: 'column'
	// 				        },
	// 				        title: {
	// 				            text: data[0].county
	// 				        },
	// 				        xAxis: {
	// 				            categories: [
	// 				                data[0].publish_time
	// 				            ],
	// 				            crosshair: true
	// 				        },
	// 				        yAxis: {
	// 				            min: 0,
	// 				            title: {
	// 				                text: 'PM2.5(ug/m^3)'
	// 				            }
	// 				        },
	// 				        tooltip: {
	// 				            // headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
	// 				            pointFormat: '<table><tr><td style="color:{series.color}; padding:0; font-size:16px">{series.name}: </td>' +
	// 				                '<td style="padding:0; font-size:16px"><b>{point.y:.1f}</b></td></tr>',
	// 				            footerFormat: '</table>',
	// 				            shared: true,
	// 				            useHTML: true
	// 				        },
	// 				        plotOptions: {
	// 				            column: {
	// 				                pointPadding: 1,
	// 				                borderWidth: 2
	// 				            }
	// 				        },
	// 				        series: seriesOptions
	// 				    });
	// 			    };

	// 			    $.each(data, function (i, name) {
	// 			            seriesOptions[i] = {
	// 			                name: data[i].sitename,
	// 			                data: [parseInt(data[i].psi)]
	// 			            };

	// 			            // As we're loading the data asynchronously, we don't know what order it will arrive. So
	// 			            // we keep a counter and create the chart when all the data is loaded.
	// 			            seriesCounter += 1;

	// 			            if (seriesCounter === data.length) {
	// 			                createPsiChart();
	// 			            }  
	// 			    });
	// 			});
	// 	    },
	// 	    error: function (e) {
	// 	    	alert("Something error!");
	// 	    }
	// 	});
	// }	

var map;

function initMap() {
	console.log("initMap");
  	map = new google.maps.Map(document.getElementById('map'), {
    	center: {lat: 23.941027, lng: 121.076728},
    	zoom: 7
  	});

  	 var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h1 id="firstHeading" class="firstHeading">Uluru</h1>'+
      '<div id="bodyContent">'+
      '<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large ' +
      'sandstone rock formation in the southern part of the '+
      'Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) '+
      'south west of the nearest large town, Alice Springs; 450&#160;km '+
      '(280&#160;mi) by road. Kata Tjuta and Uluru are the two major '+
      'features of the Uluru - Kata Tjuta National Park. Uluru is '+
      'sacred to the Pitjantjatjara and Yankunytjatjara, the '+
      'Aboriginal people of the area. It has many springs, waterholes, '+
      'rock caves and ancient paintings. Uluru is listed as a World '+
      'Heritage Site.</p>'+
      '<p>Attribution: Uluru, <a href="https://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
      'https://en.wikipedia.org/w/index.php?title=Uluru</a> '+
      '(last visited June 22, 2009).</p>'+
      '</div>'+
      '</div>';

  var infowindow = new google.maps.InfoWindow({
    content: contentString
  });
  	var marker, i;

	$.get( "{!! './js/air_quality_station_of_geographic_information.json' !!}", function(data) {
		console.log("json success");
		$.each(data, function(i, name){
			marker = new google.maps.Marker({
				position:new google.maps.LatLng(data[i].TWD97Lat, data[i].TWD97Lon),
				map:map,
				title:data[i].sitename
			});
		});
	});

	marker.addListener('click', function() {
    	infowindow.open(map, marker);
  	});
}

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection