@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-javascript")
<script src="{{ asset('highmaps/js/highmaps.js') }}"></script>
<script src="https://code.highcharts.com/mapdata/countries/tw/tw-all.js"></script>
{{-- <script src='https://raw.github.com/creationix/step/master/lib/step.js'></script> --}}
@endsection

@section("title", "空塵計")

@section("content")
	<div id = "map"  style = "width: 100%; height:400px; margin:20px"></div>
@endsection

@section("page-javascript")
<script type="text/javascript">
var map;
var infowindow = [], markers = [];
var contentString = document.createElement('div');
contentString.setAttribute('id', 'chart_div');
contentString.setAttribute('width', 800);

var div_row1 = document.createElement('div');
div_row1.setAttribute('class', 'row');

var div_row2 = document.createElement('div')
div_row2.setAttribute('class', 'row');

var chart1 = document.createElement('div');
chart1.setAttribute('id', 'chart1');
chart1.setAttribute('class', 'col-xs-12 col-md-6');

var chart2 = document.createElement('div');
chart2.setAttribute('id', 'chart2');
chart2.setAttribute('class', 'col-xs-6 col-md-6');

var chart3 = document.createElement('div');
chart3.setAttribute('id', 'chart3');
chart3.setAttribute('class', 'col-xs-6 col-md-6');

div_row1.appendChild(chart1);
div_row2.appendChild(chart2);
div_row2.appendChild(chart3);

contentString.appendChild(div_row1);
contentString.appendChild(div_row2);

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

    var table = {
        '0' : ['chart1', 'psi'],
        '1' : ['chart2', 'pm25'],
        '2' : ['chart3', 'co']
    };

	/*
    * Only content air quality station geographic
    */  
	setTimeout(function(){
		$.get( "{!! './js/air_quality_station_of_geographic_information.json' !!}", function(data) {
			try {
    			$.each(data, function(i, name){
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
                            console.log(data[i].SiteName);
                            var chart = new setChart(data[i].SiteName);
                            setTimeout(function() {
                                for(var i = 0; i < 3 ; i++) {
                                    var lut = i.toString();
                                    chart.createChart(chart.concatDataAttr(table[lut][1]), table[lut][0], table[lut][1]); 
                                }
                            }, 300)  
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
          }
        });
	}, 500);
}

function setChart(sitename) {

    // var info = [];
    
    var arr = new Array(); 
    arr[0] = [];
    arr[1] = [];
    arr[2] = [];
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.post("{!! 'past_6_hours_data' !!}", {sitename: sitename}, function(obj) {
        // info = obj;
        $.each(obj, function(item, val) {
            arr[0].push({
                'sitename': obj[item].sitename, 
                'psi': obj[item].psi, 
                'publish_time': obj[item].publish_time
            });
            arr[1].push({
                'sitename': obj[item].sitename, 
                'pm25': obj[item].pm25, 
                'publish_time': obj[item].publish_time
            });
            arr[2].push({
                'sitename': obj[item].sitename, 
                'co': obj[item].co, 
                'publish_time': obj[item].publish_time
            });           
        }); 
    });   

    this.concatDataAttr = function(colname) {
        
        switch(colname) {
            case "psi":
                console.log("log");
                return arr[0];
                break;

            case "pm25":
                return arr[1];
                break;

            case "co":
                return arr[2];
                break;

            default :
                return null;
        }
    };
}

setChart.prototype.createChart = function(data, el, colname) {
    setTimeout(function() {
        var seriesOptions = [];
        var seriesCounter = 0;
       
        switch(colname) {
            case 'psi':
                $.each(data, function (i, val) {
                    seriesOptions[i] = {
                        'name': 'PSI',
                        'data': [parseInt(val.psi)]                     
                    }    
                });
                break;
            case 'pm25':
                $.each(data, function (i, val) {
                    seriesOptions[i] = {
                        'name': 'PM2.5',
                        'data': [ parseInt(val.pm25) ]
                    };
                });
                break;
            case 'co':
                $.each(data, function (i, val) {
                    // console.log(val);
                    seriesOptions[i] = {
                        'name': 'CO',
                        'data': [ parseFloat(val.co) ]
                    }
                });
                break;
        }

        datachart = {
            chart: {
                type: 'line',
                renderTo: document.getElementById(el),
                height:350,
                // width:400
            },
            title: {
                text: colname
            },
            xAxis: {
                categories: [
                    data[5].publish_time, data[4].publish_time, data[3].publish_time,
                    data[2].publish_time, data[1].publish_time, data[0].publish_time
                    // '2016-05-04 18:00', '2016-05-04 19:00', '2016-05-04 20:00',
                    // '2016-05-04 21:00', '2016-05-04 22:00', '2016-05-04 23:00'
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
            series: seriesOptions
            
        };
       
        new Highcharts.chart(datachart);
    }, 1000);

    
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection