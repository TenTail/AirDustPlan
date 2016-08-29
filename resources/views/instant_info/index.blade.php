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
		       	for(var i = 0; i < data.length ; i++) {
	       			$('#show_data').append('<p>測站名稱 : ' + data[i].sitename + ' 所屬縣市 : ' + data[i].county + ' PSI : ' + data[i].psi + ' 發佈時間 : ' + data[i].publish_time + '</p>');
		       	}
		    },
		    error: function (e) {
		    	alert("Something error!");
		    }
		});
	}	

    // Initiate the chart
    // more APIs http://api.highcharts.com/highmaps
    // $('#map').highcharts('Map', {

    //     title : {
    //         text : 'Taiwan'
    //     },

    //     mapNavigation: {
    //         enabled: true,
    //         buttonOptions: {
    //             verticalAlign: 'bottom'
    //         }
    //     },

    //     // colorAxis: {
    //     //     min: 0
	   //  // },

	   //  plotOptions: {
	   //  	map: {
	   //  		allAreas: false,
	   //  		joinBy: ['woe-name', 'code'],
    //    			mapData: Highcharts.maps['countries/tw/tw-all'],
    //    			tooltip: {
    //         		headerFormat: '',
	   //          	pointFormatter: function () {
	   //                      var c_en = ['Pingtung County', 'Tainan City', 'Yilan County', 'Chiayi County', 'Taitung County', 'Penghu County', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin County', 'Kaohsiung City', 'Taipei County', 'Hsinchu City', 'Hsinchu County', 'Keelung City', 'Miaoli County', 'Taoyuan County', 'Changhua County', 'Hualien County', 'Nantou County'];
	   //                      var c_tw = ['屏東縣', '臺南縣', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中縣', '雲林縣', '高雄市', '新北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園縣', '彰化縣', '花蓮縣', '南投縣'];         
	   //                      return c_tw[c_en.indexOf(this.code)];
	   //                  }
    //         	},
    //         	allowPointSelect: true,
    //         	states: {
    //         		hover: {
    //         			color: '#BADA55'
    //         		} ,
    //         		select: {
    //         			color: '#EFFFEF'
    //         			// derColor: 'black',
    //          	 		// dashStyle: 'dot'
    //         		}
    //         	},
    //         	events: {
	   //          	click: function(e) {
	   //          		var county = event.point.name;
	   //          		console.log("event: "+county);
	   //          		// display(county);
	   //          		$.ajax({
	   //          			type:'post',
	            			// url:'{{-- route('instant_info/show/{id}')--}}',
	   //          			data: county,
	   //          			success: function(data) {
	   //          				alert("success");
	   //          			},
	   //          			error: function(e) {
	   //          				alert("error");
	   //          			}
	   //          		})
	   //          	}
    //         	}
	   //  	}    
	   //  },

    //     series : [{
    //         data: $.map(['Pingtung County', 'Tainan City', 'Yilan County', 'Chiayi County', 'Taitung County', 'Penghu County', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin County', 'Kaohsiung City', 'Taipei County', 'Hsinchu City', 'Hsinchu County', 'Keelung City', 'Miaoli County', 'Taoyuan County', 'Changhua County', 'Hualien County', 'Nantou County'], function (code) {
    //             return { code: code };
    //         })
    //     }]
    // });

	// function display(county) {
	// 	console.log("log1 " + county);
		
	// 	$.ajaxSetup({
	//         headers: {
	//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	//         }
	// 	});

	// 	$.ajax({
	// 		type: 'get',
	// 		url:'http://opendata.epa.gov.tw/ws/Data/AQX/?format=json',
	// 		contentType: "application/json; charset=utf-8",
	// 		// url: '{{-- route('instant_info.show') --}}',
	// 		// data: {
	// 		// 	_token: $('meta[name="csrf-token"]').attr('content'),
	// 		// 	id: county
	// 		// },
	// 		dataType:'json',
	// 		success: function(data) {
	// 			console.log("data = "+ data);
	// 			alert("success");
	// 		},
	// 		error: function(e) {
	// 			console.log(e);
	// 			alert("Something error!");
	// 		}
	// 	});
	// };
</script>

@endsection