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
	<div id="map" style="width: 100%; height:400px"></div>
@endsection

@section("page-javascript")
<script type="text/javascript">
var map;
var infowindow = [], markers = [];
var contentString = "<div id='chart_div'></div>";

function initMap() {
  	map = new google.maps.Map(document.getElementById('map'), {
    	center: {lat: 23.941027, lng: 121.076728},
    	zoom: 7
  	});

	infowindow = new google.maps.InfoWindow();

	setMarkers(map);
}

function setMarkers(map) {

	// var info = [];
    var icon = [];

	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

 //    $.ajax({
 //        url: 'instant_info',
 //        type: 'post',
 //        // data: {sitename: data[i].SiteName},
 //        // dataType: 'JSON',
 //        success: function (value) {
 //        	// console.log(value);
 //        	info = value;
 //        	// console.log("info = ");
 //        	// console.log(info);
 //        },
 //        error: function (e) {
 //            alert("Something error!");
 //        }
	// });

    $.post( "{!! 'instant_info' !!}", function(value) {
        console.log("HIHI");
        // info = value;
        for(var index = 0 ; index < 76 ; index++) {
            icon[index] = value[index].icon;
            // console.log(icon[index]);
        }
    })

	// Only content air quality station geographic
	setTimeout(function(){
		$.get( "{!! './js/air_quality_station_of_geographic_information.json' !!}", function(data) {
			// console.log("json success");
			// console.log(info);
			$.each(data, function(i, name){
                // console.log("i =" + i);
				markers[i] = new google.maps.Marker({
					position:new google.maps.LatLng(data[i].TWD97Lat, data[i].TWD97Lon),
					map:map,
					title:data[i].SiteName,
					icon:icon[i]
				});
			/*
			* Set multi markers with array
			*/
			google.maps.event.addListener(markers[i], 'click', function(i) {
				    return function() {
					   console.log("marker clicked "+ data[i].SiteName);
			    	    google.maps.event.addListener(infowindow, 'domready', function() {
                                createChart(data[i].SiteName)
                        });
				    }
  			    }(i));
			});
		});
	}, 300);
}

function createChart(sitename) {

    var info = [];
    $.post("{!! 'instant_info.past' !!}", {sitename: sitename}, function(obj) {
        info = obj;
    });

    $('#chart_div').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: sitename
        },
        xAxis: {
            categories: [
                info[0].publish_time, info[1].publish_time, info[2].publish_time, info[3].publish_time, info[4].publish_time, info[5].publish_time, info[6].publish_time, info[7].publish_time, info[8].publish_time, info[9].publish_time, info[10].publish_time,
                info[11].publish_time
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

    $.each(info, function (i, name) {
            seriesOptions[i] = {
                name: info[i].sitename,
                data: [parseInt(info[i].psi)]
            };
            // As we're loading the data asynchronously, we don't know what order it will arrive. So
            // we keep a counter and create the chart when all the data is loaded.
            seriesCounter += 1;
            if (seriesCounter === info.length) {
                createChart();
            }
    });
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection