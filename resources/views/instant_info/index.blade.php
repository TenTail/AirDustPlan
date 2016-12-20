@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-css")
<style type="text/css">
.spinner {
  width: 100%;
  height: 100%;

  position: fixed;
  margin: 100px auto;
  top:0;
  left:0;
  background: rgba(0,0,0,0.3);
}

.double-bounce1, .double-bounce2 {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background-color: #333;
  opacity: 0.6;
  position: absolute;
  top: 0;
  left: 0;
  
  -webkit-animation: sk-bounce 2.0s infinite ease-in-out;
  animation: sk-bounce 2.0s infinite ease-in-out;
}

.double-bounce2 {
  -webkit-animation-delay: -1.0s;
  animation-delay: -1.0s;
}

@-webkit-keyframes sk-bounce {
  0%, 100% { -webkit-transform: scale(0.0) }
  50% { -webkit-transform: scale(1.0) }
}

@keyframes sk-bounce {
  0%, 100% { 
    transform: scale(0.0);
    -webkit-transform: scale(0.0);
  } 50% { 
    transform: scale(1.0);
    -webkit-transform: scale(1.0);
  }
}
</style>
@endsection

@section("head-javascript")
<script src="{{ asset('highmaps/js/highmaps.js') }}"></script>
<script src="https://code.highcharts.com/mapdata/countries/tw/tw-all.js"></script>
{{-- <script src="{!! ('js/async.js') !!}"></script> --}}
@endsection

@section("title", "空塵計")

@section("content")
	<div id = "map"  style = "width: 100%; height:400px; margin:20px"></div>

    <div id="loading" class="spinner">
        {{-- <h1  style="position: fixed;top:50%;left: 40%;font-size: 8em;font-weight: bolder;">載入中...</h1> --}}
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
    </div>

    {{-- <div id="loading2" class="spinner">
      <div class="double-bounce1"></div>
      <div class="double-bounce2"></div>
    </div> --}}
@endsection

@section("page-javascript")
<script type="text/javascript">
$(document).ready( function() {
    loading(false);
})
var map;
var infowindow = [], markers = [];
var contentString = "<div id='chart_div' style='width: 800px'><div class='row'><div id='chart1'class='col-md-12'></div></div><div class='row'><div id='chart2' class='col-md-12'></div></div><div class='row'><div id='chart3' class='col-md-12'></div></div></div>";

function initMap() {
  	map = new google.maps.Map(document.getElementById('map'), {
    	center: {lat: 23.941027, lng: 121.076728},
    	zoom: 7,
        mapTypeId:google.maps.MapTypeId.TERRAIN
  	});

	infowindow = new google.maps.InfoWindow();

	setMarkers(map);
}

function setMarkers(map) {
    var icon = [];

	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	// setTimeout(function(){
		$.post( "{!! 'instant_info' !!}", function(locate) {
			try {
    			$.each(locate, function(i, v){
    				markers[i] = new google.maps.Marker({
    					position:new google.maps.LatLng(v.TWD97Lat, v.TWD97Lon),
    					map:map,
    					title:v.sitename,
    					icon:v.icon
    				});
            
    			/*
    			* Set multi markers with array
    			*/
    			google.maps.event.addListener(markers[i], 'click', function(i) {
    				    return function() {
                            loading(true);
                            var chart = new setChart(v.sitename);                            
                            infowindow.setContent(contentString);
                            infowindow.open(map, markers[i]);
                            map.setCenter(markers[i].getPosition());
    				    }
      			    }(i));
    			});

                google.maps.event.addListener(infowindow, 'closeclick', function() {  
        
                }); 
		  }
          catch(err) {
                console.log(err.message);
          }
        });
	// }, 700);
}

function setChart(sitename) {
    var table = {
        '0' : ['chart1', 'psi'],
        '1' : ['chart2', 'pm25', '微克/立方公尺'],
        '2' : ['chart3', 'pm10', '微克/立方公尺'],
        '3' : ['', 'co', 'ppm']
    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.when($.post("{!! 'past_6_hours_data' !!}", {sitename: sitename}))
    .done(function (data) {
        for(var i = 0 ; i < 3 ; i++) {
            var lut = i.toString();
            datachart = {
                chart: {
                    type: 'line',
                    renderTo: document.getElementById(table[lut][0]),
                    // height:350,
                    // width:400
                },
                title: {
                    text: sitename
                },
                xAxis: {
                    categories: data[i][0],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: table[lut][2]
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
                series: [{
                    name:table[lut][1],
                    data:data[i][1] 
                }]                   
            };

            new Highcharts.chart(datachart);
        };
        loading(false);
    });   
}

// when data is loading block the full page.
function loading(isdisplay = true) {
    $('#loading').css('display', isdisplay ? 'block' : 'none');
    // $('#loading2').css('display', isdisplay ? 'block' : 'none');
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection