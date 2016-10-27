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
var map;
var stationGeographicInfo = [], stationGeographicInfoCounter = 0;
var infowindow = [], markers = [];

function initMap() {
	console.log("initMap");
  	map = new google.maps.Map(document.getElementById('map'), {
    	center: {lat: 23.941027, lng: 121.076728},
    	zoom: 7
  	});

	infowindow = new google.maps.InfoWindow();

	setMarkers(map);
}

function setMarkers(map, stationGeographicInfo) {

	var info = [];

	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: 'instant_info',
        type: 'post',
        // data: {sitename: data[i].SiteName},
        // dataType: 'JSON',
        success: function (value) {
            // alert("success");
            console.log(value);
            for(var index = 0 ; index < value.length ; index++) {

            	var icon_base = './img/icons/';
            	info[index] = [];
            	// var icon = null;

            	/*
				* green
				*/
				if(value[index].psi < 51) {
					info[index].push({
						'sitename' : value[index].sitename,
						'county' : value[index].county,
						'psi' : value[index].psi,
						'publish_time' : value[index].publish_time,
						'icon' : icon_base + 'green.jpg'
					});
					// console.log("info["+index+"][0] = "+info[index][0].sitename+" "+info[index][0].publish_time);
				}

				/*
				* yellow
				*/
				if(value[index].psi > 50 && value[index].psu < 101) {;
					info[index].push({
						'sitename' : value[index].sitename,
						'county' : value[index].county,
						'psi' : value[index].psi,
						'publish_time' : value[index].publish_time,
						'icon' : icon_base + 'yellow.jpg'
					});
					// console.log("info["+index+"][0] = "+info[index][0].sitename+" "+info[index][0].publish_time);
				}

				/*
				* red
				*/
				if(value[index].psi > 100 && value[index].psu < 200) {
					info[index].push({
						'sitename' : value[index].sitename,
						'county' : value[index].county,
						'psi' : value[index].psi,
						'publish_time' : value[index].publish_time,
						'icon' : icon_base + 'red.jpg'
					});
					// console.log("info["+index+"][0] = "+info[index][0].sitename+" "+info[index][0].publish_time);
				}

				/*
				* purple
				*/
				if(value[index].psi > 199 && value[index].psu < 300) {;
					info[index].push({
						'sitename' : value[index].sitename,
						'county' : value[index].county,
						'psi' : value[index].psi,
						'publish_time' : value[index].publish_time,
						'icon' : icon_base + 'purple.jpg'
					});
					// console.log("info["+index+"][0] = "+info[index][0].sitename+" "+info[index][0].publish_time);
				}

				/*
				* brown
				*/
				if(value[index].psi > 299) {
					info[index].push({
						'sitename' : value[index].sitename,
						'county' : value[index].county,
						'psi' : value[index].psi,
						'publish_time' : value[index].publish_time,
						'icon' : icon_base + 'brown.jpg'
					});
					// console.log("info["+index+"][0] = "+info[index][0].sitename+" "+info[index][0].publish_time);
				}
				if(value[index].psi) {
					info[index].push({
						'sitename' : value[index].sitename,
						'county' : value[index].county,
						'psi' : value[index].psi,
						'publish_time' : value[index].publish_time,
						'icon' : icon_base + 'yellow.jpg'
					});
					// console.log("info["+index+"][0] = "+info[index][0].sitename+" "+info[index][0].publish_time);
				}

            }
            // console.log("After 0.3 sec "+psi_value);
        },
        error: function (e) {
            alert("Something error!");
        }
	});

	// Only content air quality station geographic
	setTimeout(function(){
		$.get( "{!! './js/air_quality_station_of_geographic_information.json' !!}", function(data) {
			console.log("json success");
			// console.log(info);
			$.each(data, function(i, name){
				// console.log(info[0][0].icon);
				// if(data[i].SiteName == info[i].sitename) {
				markers[i] = new google.maps.Marker({
					position:new google.maps.LatLng(data[i].TWD97Lat, data[i].TWD97Lon),
					map:map,
					title:data[i].SiteName,
					icon:info[i][0].icon
				});
			// }

			/*
			* Set multi markers with array
			*/
			google.maps.event.addListener(markers[i], 'click', function(i) {
				return function() {
					console.log("marker clicked "+ data[i].SiteName);
			    	setTimeout(function(){
  						infowindow.setContent("HIHI"+ data[i].SiteName);
    					infowindow.open(map, markers[i]);
					}, 300);

				}
  			}(i));
			});
		});
	}, 300);


}

function getInstantValue(sitename) {
	console.log(sitename);
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: 'instant_info',
        type: 'post',
        data: {sitename: sitename},
        // dataType: 'JSON',
        success: function (data) {
            alert("success");
            console.log(data[0].psi);
            return data[0].psi;
        },
        error: function (e) {
            alert("Something error!");
        }
    });
}

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection