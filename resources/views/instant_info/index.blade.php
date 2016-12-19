@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-javascript")
<script src="{{ asset('highmaps/js/highmaps.js') }}"></script>
<script src="https://code.highcharts.com/mapdata/countries/tw/tw-all.js"></script>
{{-- <script src="{!! ('js/async.js') !!}"></script> --}}
@endsection

@section("title", "空塵計")

@section("content")
	<div id = "map"  style = "width: 100%; height:400px; margin:20px"></div>
@endsection

@section("page-javascript")
<script type="text/javascript">
var map;
var infowindow = [], markers = [];
var contentString = "<div id='chart_div' style='width: 800px'><div class='row'><div id='chart1'class='col-md-12'></div></div><div class='row'><div id='chart2' class='col-md-6'></div><div id='chart3' class='col-md-6'></div></div></div>";

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

    $.post( "{!! 'instant_info' !!}", function(value) {
        for(var index = 0 ; index < 76 ; index++) {
            icon[index] = value[index].icon;
        }
    });

	/*
    * Only content air quality station geographic
    */  
	setTimeout(function(){
		$.get( "{!! './js/air_quality_station_of_geographic_information.json' !!}", function(locate) {
			try {
    			$.each(locate, function(i, name){
    				markers[i] = new google.maps.Marker({
    					position:new google.maps.LatLng(locate[i].TWD97Lat, locate[i].TWD97Lon),
    					map:map,
    					title:locate[i].SiteName,
    					icon:icon[i]
    				});
            
    			/*
    			* Set multi markers with array
    			*/
    			google.maps.event.addListener(markers[i], 'click', function(i) {
    				    return function() {
                            var chart = new setChart(locate[i].SiteName);                            
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
	}, 700);
}

function setChart(sitename) {
    var data;

    var table = {
        '0' : ['chart1', 'psi'],
        '1' : ['chart2', 'pm25', '微克/立方公尺'],
        '2' : ['chart3', 'co', 'ppm']
    };

    wait([
        function (r, next) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.post("{!! 'past_6_hours_data' !!}", {sitename: r}, function(obj) {
                next(obj);
            });
        }],
        sitename,
        function (result) {
            for(var i = 0 ; i < 3 ; i++) {
                var lut = i.toString();
                datachart = {
                    chart: {
                        type: 'line',
                        renderTo: document.getElementById(table[lut][0]),
                        height:350,
                        // width:400
                    },
                    title: {
                        text: sitename
                    },
                    xAxis: {
                        categories: result[i][0],
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
                        data:result[i][1] 
                    }]                   
                };

                new Highcharts.chart(datachart);
            }
        }
    );     
}


/*
*   fn 是一個需要依序執行的函數陣列
*   r  傳遞給第一個執行函數的參數
*   cb 處理結果的函數
*/
function wait(fn, r, cb) {
    var count = 0;
    next(r);
    function next(r) {
        if(count < fn.length) {
            fn[count](r, next);
            count++;           
        } else {
            cb(r);
        }
    }
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection