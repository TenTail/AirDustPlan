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
var stationGeographicInfo = [], stationGeographicInfoCounter = 0;
var infowindow = [], markers = [];

function initMap() {
  	map = new google.maps.Map(document.getElementById('map'), {
    	center: {lat: 23.941027, lng: 121.076728},
    	zoom: 7
  	});

	infowindow = new google.maps.InfoWindow();

	setMarkers(map);
}

function setMarkers(map, stationGeographicInfo) {

	var info = null;

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
        	// console.log(value);
        	info = value;
        	// console.log("info = ");
        	// console.log(info);
        },
        error: function (e) {
            alert("Something error!");
        }
	});

	// Only content air quality station geographic
	setTimeout(function(){
		$.get( "{!! './js/air_quality_station_of_geographic_information.json' !!}", function(data) {
			// console.log("json success");
			// console.log(info);
			$.each(data, function(i, name){
				markers[i] = new google.maps.Marker({
					position:new google.maps.LatLng(data[i].TWD97Lat, data[i].TWD97Lon),
					map:map,
					title:data[i].SiteName,
					icon:info[i].icon
				});
			/*
			* Set multi markers with array
			*/
			google.maps.event.addListener(markers[i], 'click', function(i) {
				return function() {
					console.log("marker clicked "+ data[i].SiteName);
			    	setTimeout(function(){
  						infowindow.setContent("HIHI"+ data[i].SiteName + info[i].psi);
    					infowindow.open(map, markers[i]);
					}, 300);

				}
  			}(i));
			});
		});
	}, 300);
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection