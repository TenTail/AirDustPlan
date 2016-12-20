@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-javascript")
{{-- <script src="//d3js.org/d3.v4.min.js"></script> --}}
{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script> --}}
{{-- <script src="{!! ('js/async.js') !!}"></script> --}}
@endsection

@section("title", "即時資訊 with D3js")

@section("content")
	<div id = "map"  style = "width: 100%; height:400px; margin:20px"></div>
@endsection

@section("page-javascript")
<script src="//d3js.org/d3.v4.min.js"></script>
<script type="text/javascript">
var contentString = "<div id='chart_div' style='width: 800px'><div class='row'><div id='chart1'class='col-md-12'></div></div><div class='row'><div id='chart2' class='col-md-6'></div><div id='chart3' class='col-md-6'></div></div></div>";

function initMap() {
	$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	var markers = [];
  	map = new google.maps.Map(document.getElementById('map'), {
    	center: {lat: 23.941027, lng: 121.076728},
    	zoom: 7,
        mapTypeId:google.maps.MapTypeId.TERRAIN
  	});

	infowindow = new google.maps.InfoWindow();

	$.when( $.post( "{!! 'instant_info' !!}" ))
	.then(function(locate) {
		console.log(locate);
		// console.log(icons);
		$.each(locate, function(index, v) {
			markers[index] = new google.maps.Marker({
				position:new google.maps.LatLng(v.TWD97Lat, v.TWD97Lon),
				map:map,
				title:v.SiteName,
				icon:v.icon
			});

			google.maps.event.addListener(markers[index], 'click', function(i) {
				    return function() {
                        var chart = new setChart(v.sitename);                            
                        // infowindow.setContent(contentString);
                        infowindow.open(map, markers[i]);
                        map.setCenter(markers[i].getPosition());
				    }
  			    }(index));
			});

        google.maps.event.addListener(infowindow, 'closeclick', function() {  

        });
		
	});
}

function setChart(sitename) {
	var margin = {
        top: 30,
        right: 20,
        bottom: 30,
        left: 50
    };

    var width = 800 - margin.left - margin.right;
    var height = 600 - margin.top - margin.bottom;

    $.when($.post("{!! 'past_6_hours_data' !!}", {sitename: sitename}))
     .then(
     	function(data) {
     		
     		var x = d3.scaleTime()
     				.range([0, width]);
 	     	var y = d3.scaleTime()
 		    		.range([0, height]);
 		    var xAxis = d3.axisBottom(x).ticks(5);

		    var yAxis = d3.axisLeft(y).ticks(5);

    		var valueline = d3.line()
                        .x(function (d) {
                        	console.log(d[0]);
                            return x(d[0]);
                        })
                        .y(function (d) {
                        	console.log(d);
                        	console.log(d[1])
                        	console.log(d[1][1]);
                            return y(d[1]);
                        });
 	
 	     	var container = d3.select(document.createElement("div"))
 	        .attr("width", 800)
 	        .attr("height", 600);
 	
 	        var svg = container.append("svg")
 	        		.attr("width", width + margin.left + margin.right)
 	        		.attr("height", height + margin.top + margin.bottom)
 	        		.append("g")
 	        		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
 	
 	     //    data.forEach(function (d) {
 	     //    	console.log(d)
 		    //     d.date = d[0];
 		    //     d.pm25 = d[1];
 		    // });
 	
 		    // Scale the range of the data
 		    x.domain(d3.extent(data, function (d) {
 		        return d[0];
 		    }));
 		    y.domain([0, d3.max(data, function (d) {
 		        return d[0][1];
 		    })]);

 		    // Add the valueline path.
 		    svg.append("path").attr("d", valueline(data));

 		    // Add the X Axis
 		    svg.append("g")
 		    	.attr("class", "x axis")
		        .attr("transform", "translate(0," + height + ")")
		        .call(xAxis);

		    // Add the Y Axis
		    svg.append("g")
		    	.attr("class", "y axis")
        		.call(yAxis);

        	var graphHtml = container.node().outerHTML; 
    		infowindow.setContent(graphHtml);
 		}
     )
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCD5dI4ddETACuDY-rUlZH-2Ept65w150Q&callback=initMap" async defer></script>
@endsection