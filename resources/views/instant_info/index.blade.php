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
	<div id = "map"  style = "width: 100%; height:400px; margin:20px"></div>
@endsection

@section("page-javascript")
<script type="text/javascript">
var map;
var infowindow = [], markers = [];
// var contentString = "<div id='chart_div'></div>";
var contentString = document.createElement('div');
contentString.setAttribute('id', 'chart_div');
contentString.setAttribute('height', 350);
contentString.setAttribute('width', 350);

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

    $.post( "{!! 'instant_info' !!}", function(value) {
        console.log("HIHI");
        // info = value;
        for(var index = 0 ; index < 76 ; index++) {
            icon[index] = value[index].icon;
            // console.log(icon[index]);
        }
    })

	/*
    * Only content air quality station geographic
    */  
	setTimeout(function(){
		$.get( "{!! './js/air_quality_station_of_geographic_information.json' !!}", function(data) {
			try {
    			$.each(data, function(i, name){
                    // console.log("icon " + icon[i]);
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
                            console.log(contentString);
                            var chart = new Chart(data[i].SiteName);
    			    	    infowindow.setContent(contentString);
                            infowindow.open(map, markers[i]);
    				    }
      			    }(i));
    			});

                google.maps.event.addListener(infowindow, 'closeclick', function() {  
        
                }); 
		  }
          catch(err) {
                console.log(err.message);
                // $('#error').innerHTML = err.message;
          }
        });
	}, 1000);
}

function Chart(sitename) {

    var info = [];
    var seriesOptions = [];
    var seriesCounter = 0;
    console.log("Chart = " + sitename);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.post("{!! 'past_12_hours_data' !!}", {sitename: sitename}, function(obj) {
        info = obj;
        console.log(info);
    });

    setTimeout(function createChart() {
        console.log("createChart");
        // google.maps.event.addListener(infowindow, 'domready', function() {
            console.log("info[0].publish_time");
            $('#chart_div').highcharts({
                chart: {
                    type: 'line'
                },
                title: {
                    text: sitename
                },
                xAxis: {
                    categories: [
                        info[0].publish_time, info[1].publish_time, info[2].publish_time,
                        info[3].publish_time, info[4].publish_time, info[5].publish_time,
                    ],
                    crosshair: true
                },
                yAxis: {
                    // min: 0,
                    // title: {
                    //     text: 'PSI'
                    // }
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
                // series: seriesOptions
                series: [
                    {
                        name: 'PSI',
                        data: [
                            parseInt(info[0].psi), parseInt(info[1].psi), parseInt(info[2].psi), parseInt(info[3].psi),
                            parseInt(info[4].psi), parseInt(info[5].psi)
                        ]
                    }, 
                    {
                        name: 'CO',
                        data: [
                            info[0].co, info[1].co, info[2].co, 
                            info[3].co, info[4].co, info[5].co
                        ]
                    }, 
                    {
                        name: 'PM2.5',
                        data: [
                            parseInt(info[0].pm25), parseInt(info[1].pm25), parseInt(info[2].pm25), parseInt(info[3].pm25),
                            parseInt(info[4].pm25), parseInt(info[5].pm25)
                        ]
                    }
                ]
            });
        
        /*
        $.each(info, function (i, name) {
                seriesOptions[i] =     
                [
                    name: "PSI",
                    data: [parseInt(info[i].psi)]
                ];
                
                // As we're loading the data asynchronously, we don't know what order it will arrive. So
                // we keep a counter and create the chart when all the data is loaded.
                seriesCounter += 1;
                if (seriesCounter === info.length) {
                    // createChart();
                }
        });
        */
    }, 1500)
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection