@extends("layouts.master")

@section("csrf-token")
<meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection

@section("head-css")
<style type="text/css">
	.FPMI1 { background-color:rgb(156, 255, 156); }
	.FPMI2 { background-color:rgb(49, 255, 0); }
	.FPMI3 { background-color:rgb(49, 207, 0); }
	.FPMI4 { background-color:rgb(255, 255, 0); }
	.FPMI5 { background-color:rgb(255, 207, 0); }
	.FPMI6 { background-color:rgb(255, 154, 0); }
	.FPMI7 { background-color:rgb(255, 100, 100); }
	.FPMI8 { background-color:rgb(255, 0, 0); color: rgb(255, 255, 255);}
	.FPMI9 { background-color:rgb(153, 0, 0); color: rgb(255, 255, 255);}
	.FPMI10 { background-color:rgb(206, 48, 255); color: rgb(255, 255, 255);}
</style>
@endsection

@section("head-javascript")
<script src="{{ asset('highmaps/js/highmaps.js') }}"></script>
<script src="https://code.highcharts.com/mapdata/countries/tw/tw-all.js"></script>
@endsection

@section("title", "空塵計")

@section("content")

<section class="col-md-12" id="epa_pm25_index">
<table border="1" style="font-size:12pt;">
	<tr>
		<th align="center" width="8%">指標等級</th>
		<th width="6%">1</th>
		<th width="6%">2</th>
		<th width="6%">3</th>
		<th align="center" width="7%">4</th>
		<th align="center" width="7%">5</th>
		<th align="center" width="7%">6</th>
		<th align="center" width="8%">7</th>
		<th align="center" width="8%">8</th>
		<th align="center" width="8%">9</th>
		<th align="center">10</th>
	</tr>
	<tr>
		<th align="center" width="8%">分類</th>
		<td align="center" width="6%" class="FPMI1">低</td>
		<td align="center" width="6%" class="FPMI2">低</td>
		<td align="center" width="6%" class="FPMI3">低</td>
		<td align="center" width="7%" class="FPMI4">中</td>
		<td align="center" width="7%" class="FPMI5">中</td>
		<td align="center" width="7%" class="FPMI6">中</td>
		<td align="center" width="8%" class="FPMI7">高</td>
		<td align="center" width="8%" class="FPMI8">高</td>
		<td align="center" width="8%" class="FPMI9">高</td>
		<td align="center" class="FPMI10">非常高</td>	
	</tr>
	<tr>
		<th align="center" width="8%">PM<sub>2.5</sub>濃度<br />
		<span style="font-size:75%;">(μg/m<sup>3</sup>)</span></th>
		<td align="center" width="6%" class="FPMI1">0-11</td>
		<td align="center" width="6%" class="FPMI2">12-23</td>
		<td align="center" width="6%" class="FPMI3">24-35</td>
		<td align="center" width="7%" class="FPMI4">36-41</td>
		<td align="center" width="7%" class="FPMI5">42-47</td>
		<td align="center" width="7%" class="FPMI6">48-53</td>
		<td align="center" width="8%" class="FPMI7">54-58</td>
		<td align="center" width="8%" class="FPMI8">59-64</td>
		<td align="center" width="8%" class="FPMI9">65-70</td>
		<td align="center" class="FPMI10">≧71</td>	</tr>
	<tr>
		<th align="center" width="8%" valign="top">一般民眾<br>
		活動建議</th>
		<td colspan="3" width="7%" valign="top">正常戶外活動。</td>
		<td colspan="3" width="7%" valign="top">正常戶外活動。</td>
		<td colspan="3" width="8%" valign="top">
		    任何人如果有不適，如眼痛，咳嗽或喉嚨痛等，應該考慮減少戶外活動。</td>
		<td valign="top">
		    任何人如果有不適，如眼痛，咳嗽或喉嚨痛等，應減少體力消耗，特別是減少戶外活動。
		</td>	
	</tr>
	<tr>
		<th align="center" width="9%" valign="top">敏感性族群<br />
		活動建議</th>
		<td colspan="3" width="7%" valign="top">正常戶外活動。</td>
		<td colspan="3" width="7%" valign="top">
		    有心臟、呼吸道及心血管疾病的成人與孩童感受到癥狀時，應考慮減少體力消耗，特別是減少戶外活動。</td>
		<td colspan="3" width="8%" valign="top">
		    1. 有心臟、呼吸道及心血管疾病的成人與孩童，應減少體力消耗，特別是減少戶外活動。<br />
            2. 老年人應減少體力消耗。 
            <br />
            3. 具有氣喘的人可能需增加使用吸入劑的頻率。 </td>
		<td valign="top">1. 有心臟、呼吸道及心血管疾病的成人與孩童，以及老年人應避免體力消耗，特別是避免戶外活動。<br />
            2. 具有氣喘的人可能需增加使用吸入劑的頻率。 </td>	
    </tr>
	</table>
	<h3>資料來源:<a href="http://taqm.epa.gov.tw/taqm/tw/fpmi.htm">環保署</a></h3>
</section>

<section class="col-md-12">
	<div class="col-md-6" style="height: 500px; min-width: 310px; max-width: 480px; margin: 0 auto" id="map">
	</div>
	<div class="col-md-6" id="stations">
		<p>123</p>
		@if(isset($data))
			@foreach($data as $value)
				<span>{{ $value->station }}</span>
				<span>{{ $value->country }}</span>
				<span>{{ $value->pm25 }}</span>
			@endforeach
		@endif
	</div>
</section>

@endsection

@section("page-javascript")
<script>
  
    // Initiate the chart
    // more APIs http://api.highcharts.com/highmaps
    $('#map').highcharts('Map', {

        title : {
            text : 'Taiwan'
        },

        mapNavigation: {
            enabled: true,
            buttonOptions: {
                verticalAlign: 'bottom'
            }
        },

        // colorAxis: {
        //     min: 0
	    // },

	    plotOptions: {
	    	map: {
	    		allAreas: false,
	    		joinBy: ['woe-name', 'code'],
       			mapData: Highcharts.maps['countries/tw/tw-all'],
       			tooltip: {
            		headerFormat: '',
	            	pointFormatter: function () {
	                        var c_en = ['Pingtung County', 'Tainan City', 'Yilan County', 'Chiayi County', 'Taitung County', 'Penghu County', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin County', 'Kaohsiung City', 'Taipei County', 'Hsinchu City', 'Hsinchu County', 'Keelung City', 'Miaoli County', 'Taoyuan County', 'Changhua County', 'Hualien County', 'Nantou County'];
	                        var c_tw = ['屏東縣', '臺南縣', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中縣', '雲林縣', '高雄市', '新北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園縣', '彰化縣', '花蓮縣', '南投縣'];         
	                        return c_tw[c_en.indexOf(this.code)];
	                    }
            	},
            	allowPointSelect: true,
            	states: {
            		hover: {
            			color: '#BADA55'
            		} ,
            		select: {
            			color: '#EFFFEF'
            			// derColor: 'black',
             	 		// dashStyle: 'dot'
            		}
            	},
            	events: {
	            	click: function(e) {
	            		var county = event.point.name;
	            		console.log("event: "+county);
	            		// display(county);
	            		$.ajax({
	            			type:'post',
	            			url:'{{ route('instant_info/show/{id}')}}',
	            			data: county,
	            			success: function(data) {
	            				alert("success");
	            			},
	            			error: function(e) {
	            				alert("error");
	            			}
	            		})
	            	}
            	}
	    	}    
	    },

        series : [{
            data: $.map(['Pingtung County', 'Tainan City', 'Yilan County', 'Chiayi County', 'Taitung County', 'Penghu County', 'Taipei City', 'Chiayi City', 'Taichung City', 'Yunlin County', 'Kaohsiung City', 'Taipei County', 'Hsinchu City', 'Hsinchu County', 'Keelung City', 'Miaoli County', 'Taoyuan County', 'Changhua County', 'Hualien County', 'Nantou County'], function (code) {
                return { code: code };
            })
        }]
    });

 

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