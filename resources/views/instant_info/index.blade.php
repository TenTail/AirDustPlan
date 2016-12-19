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
var stationgeo = [{"SiteName":"臺東","SiteAddress":"臺東市中山路276號","TWD97Lon":"121.1504500000","TWD97Lat":"22.7553580000"},{"SiteName":"臺南","SiteAddress":"臺南市中西區南寧街45號","TWD97Lon":"120.2026170000","TWD97Lat":"22.9845810000"},{"SiteName":"臺西","SiteAddress":"雲林縣臺西鄉五港路505號","TWD97Lon":"120.2028420000","TWD97Lat":"23.7175330000"},{"SiteName":"觀音","SiteAddress":"桃園市觀音區文化路2號","TWD97Lon":"121.0827610000","TWD97Lat":"25.0355030000"},{"SiteName":"關山","SiteAddress":"臺東縣關山鎮自強路66號","TWD97Lon":"121.1619330000","TWD97Lat":"23.0450830000"},{"SiteName":"豐原","SiteAddress":"臺中市豐原區水源路150號","TWD97Lon":"120.7417110000","TWD97Lat":"24.2565860000"},{"SiteName":"龍潭","SiteAddress":"桃園市龍潭區中正路210號","TWD97Lon":"121.2163500000","TWD97Lat":"24.8638690000"},{"SiteName":"頭份","SiteAddress":"苗栗縣頭份鎮文化街20號","TWD97Lon":"120.8985720000","TWD97Lat":"24.6969690000"},{"SiteName":"橋頭","SiteAddress":"高雄市橋頭區隆豐北路1號","TWD97Lon":"120.3056890000","TWD97Lat":"22.7575060000"},{"SiteName":"線西","SiteAddress":"彰化縣線西鄉寓埔村中央路二段145號","TWD97Lon":"120.4690610000","TWD97Lat":"24.1316720000"},{"SiteName":"潮州","SiteAddress":"屏東縣潮州鎮九塊里復興路66號","TWD97Lon":"120.5611750000","TWD97Lat":"22.5231080000"},{"SiteName":"鳳山","SiteAddress":"高雄市鳳山區曹公路6號","TWD97Lon":"120.3580830000","TWD97Lat":"22.6273920000"},{"SiteName":"彰化","SiteAddress":"彰化縣彰化市文心街55號","TWD97Lon":"120.5415190000","TWD97Lat":"24.0660000000"},{"SiteName":"嘉義","SiteAddress":"嘉義市西區新民路580號","TWD97Lon":"120.4408330000","TWD97Lat":"23.4627780000"},{"SiteName":"萬華","SiteAddress":"臺北市萬華區中華路1段66號","TWD97Lon":"121.5079720000","TWD97Lat":"25.0465030000"},{"SiteName":"萬里","SiteAddress":"新北市萬里區瑪鋉路221號","TWD97Lon":"121.6898810000","TWD97Lat":"25.1796670000"},{"SiteName":"楠梓","SiteAddress":"高雄市楠梓區楠梓路262號","TWD97Lon":"120.3282890000","TWD97Lat":"22.7336670000"},{"SiteName":"新營","SiteAddress":"臺南市新營區中正路4號","TWD97Lon":"120.3172500000","TWD97Lat":"23.3056330000"},{"SiteName":"新港","SiteAddress":"嘉義縣新港鄉登雲路105號","TWD97Lon":"120.3455310000","TWD97Lat":"23.5548390000"},{"SiteName":"新莊","SiteAddress":"新北市新莊區中正路510號","TWD97Lon":"121.4325000000","TWD97Lat":"25.0379720000"},{"SiteName":"新店","SiteAddress":"新北市新店區民族路108號","TWD97Lon":"121.5377780000","TWD97Lat":"24.9772220000"},{"SiteName":"新竹","SiteAddress":"新竹市民族路33號","TWD97Lon":"120.9720750000","TWD97Lat":"24.8056190000"},{"SiteName":"陽明","SiteAddress":"臺北市北投區竹子湖路111號","TWD97Lon":"121.5295830000","TWD97Lat":"25.1827220000"},{"SiteName":"菜寮","SiteAddress":"新北市三重區中正北路163號","TWD97Lon":"121.4810280000","TWD97Lat":"25.0689500000"},{"SiteName":"善化","SiteAddress":"臺南市善化區益名寮60號","TWD97Lon":"120.2971420000","TWD97Lat":"23.1150970000"},{"SiteName":"湖口","SiteAddress":"新竹縣湖口鄉成功路360號","TWD97Lon":"121.0386530000","TWD97Lat":"24.9001420000"},{"SiteName":"復興","SiteAddress":"高雄市前鎮區民權二路331號","TWD97Lon":"120.3120170000","TWD97Lat":"22.6087110000"},{"SiteName":"麥寮","SiteAddress":"雲林縣麥寮鄉中興路115號","TWD97Lon":"120.2518250000","TWD97Lat":"23.7535060000"},{"SiteName":"淡水","SiteAddress":"新北市淡水區中正東路42巷6號","TWD97Lon":"121.4492390000","TWD97Lat":"25.1645000000"},{"SiteName":"崙背","SiteAddress":"雲林縣崙背鄉南陽村大成路91號","TWD97Lon":"120.3487420000","TWD97Lat":"23.7575470000"},{"SiteName":"基隆","SiteAddress":"基隆市東信路324號","TWD97Lon":"121.7600560000","TWD97Lat":"25.1291670000"},{"SiteName":"馬祖","SiteAddress":"連江縣南竿鄉介壽村13號","TWD97Lon":"119.9498750000","TWD97Lat":"26.1604690000"},{"SiteName":"馬公","SiteAddress":"澎湖縣馬公市中正路115號","TWD97Lon":"119.5661580000","TWD97Lat":"23.5690310000"},{"SiteName":"桃園","SiteAddress":"桃園市桃園區成功路二段144號","TWD97Lon":"121.3199640000","TWD97Lat":"24.9947890000"},{"SiteName":"埔里","SiteAddress":"南投縣埔里鎮西安路一段193號","TWD97Lon":"120.9679030000","TWD97Lat":"23.9688420000"},{"SiteName":"苗栗","SiteAddress":"苗栗市縣府路100號","TWD97Lon":"120.8202000000","TWD97Lat":"24.5652690000"},{"SiteName":"美濃","SiteAddress":"高雄市美濃區中壇里忠孝路19號","TWD97Lon":"120.5305420000","TWD97Lat":"22.8835830000"},{"SiteName":"恆春","SiteAddress":"屏東縣恆春鎮公園路44號","TWD97Lon":"120.7889280000","TWD97Lat":"21.9580690000"},{"SiteName":"屏東","SiteAddress":"屏東市蘇州街75號","TWD97Lon":"120.4880330000","TWD97Lat":"22.6730810000"},{"SiteName":"南投","SiteAddress":"南投市南陽路269號","TWD97Lon":"120.6853060000","TWD97Lat":"23.9130000000"},{"SiteName":"前鎮","SiteAddress":"高雄市前鎮區中山三路43號","TWD97Lon":"120.3075640000","TWD97Lat":"22.6053860000"},{"SiteName":"前金","SiteAddress":"高雄市前金區河南二路196號","TWD97Lon":"120.2880860000","TWD97Lat":"22.6325670000"},{"SiteName":"金門","SiteAddress":"金門縣金城鎮民權路32號","TWD97Lon":"118.3122560000","TWD97Lat":"24.4321330000"},{"SiteName":"花蓮","SiteAddress":"花蓮市中正路210號","TWD97Lon":"121.5997690000","TWD97Lat":"23.9713060000"},{"SiteName":"松山","SiteAddress":"臺北市松山區八德路四段746號","TWD97Lon":"121.5786110000","TWD97Lat":"25.0500000000"},{"SiteName":"板橋","SiteAddress":"新北市板橋區文化路一段25號","TWD97Lon":"121.4586670000","TWD97Lat":"25.0129720000"},{"SiteName":"林園","SiteAddress":"高雄市林園區北汕路58巷2號","TWD97Lon":"120.4117500000","TWD97Lat":"22.4795000000"},{"SiteName":"林口","SiteAddress":"新北市林口區民治路25號","TWD97Lon":"121.3768690000","TWD97Lat":"25.0771970000"},{"SiteName":"忠明","SiteAddress":"臺中市南屯區公益路二段296號","TWD97Lon":"120.6410920000","TWD97Lat":"24.1519580000"},{"SiteName":"宜蘭","SiteAddress":"宜蘭縣宜蘭市復興路二段77號","TWD97Lon":"121.7463940000","TWD97Lat":"24.7479170000"},{"SiteName":"沙鹿","SiteAddress":"臺中市沙鹿區英才路150號","TWD97Lon":"120.5687940000","TWD97Lat":"24.2256280000"},{"SiteName":"西屯","SiteAddress":"臺中市西屯區安和路1號","TWD97Lon":"120.6169170000","TWD97Lat":"24.1621970000"},{"SiteName":"竹東","SiteAddress":"新竹縣竹東鎮榮樂里三民街70號","TWD97Lon":"121.0889030000","TWD97Lat":"24.7406440000"},{"SiteName":"竹山","SiteAddress":"南投縣竹山鎮大明路666號","TWD97Lon":"120.6773060000","TWD97Lat":"23.7563890000"},{"SiteName":"汐止","SiteAddress":"新北市汐止區樟樹一路141巷2號","TWD97Lon":"121.6423000000","TWD97Lat":"25.0671310000"},{"SiteName":"朴子","SiteAddress":"嘉義縣朴子市光復路34號","TWD97Lon":"120.2473500000","TWD97Lat":"23.4653080000"},{"SiteName":"安南","SiteAddress":"臺南市安南區安和路三段193號","TWD97Lon":"120.2175000000","TWD97Lat":"23.0481970000"},{"SiteName":"永和","SiteAddress":"新北市永和區永和路光復路交口","TWD97Lon":"121.5163060000","TWD97Lat":"25.0170000000"},{"SiteName":"平鎮","SiteAddress":"桃園市平鎮區文化街189號","TWD97Lon":"121.2039860000","TWD97Lat":"24.9527860000"},{"SiteName":"左營","SiteAddress":"高雄市左營區翠華路687號","TWD97Lon":"120.2929170000","TWD97Lat":"22.6748610000"},{"SiteName":"古亭","SiteAddress":"臺北市大安區羅斯福路三段153號","TWD97Lon":"121.5295560000","TWD97Lat":"25.0206080000"},{"SiteName":"冬山","SiteAddress":"宜蘭縣冬山鄉南興村照安路26號","TWD97Lon":"121.7929280000","TWD97Lat":"24.6322030000"},{"SiteName":"斗六","SiteAddress":"雲林縣斗六市民生路224號","TWD97Lon":"120.5449940000","TWD97Lat":"23.7118530000"},{"SiteName":"仁武","SiteAddress":"高雄市仁武區八卦里永仁街555號","TWD97Lon":"120.3326310000","TWD97Lat":"22.6890560000"},{"SiteName":"中壢","SiteAddress":"桃園市中壢區延平路622號","TWD97Lon":"121.2216670000","TWD97Lat":"24.9532780000"},{"SiteName":"中山","SiteAddress":"臺北市中山區林森北路511號","TWD97Lon":"121.5265280000","TWD97Lat":"25.0623610000"},{"SiteName":"小港","SiteAddress":"高雄市小港區平和南路185號","TWD97Lon":"120.3377360000","TWD97Lat":"22.5658330000"},{"SiteName":"大寮","SiteAddress":"高雄市大寮區潮寮路61號","TWD97Lon":"120.4250810000","TWD97Lat":"22.5657470000"},{"SiteName":"大園","SiteAddress":"桃園市大園區中正東路160號","TWD97Lon":"121.2018110000","TWD97Lat":"25.0603440000"},{"SiteName":"大里","SiteAddress":"臺中市大里區大新街36號","TWD97Lon":"120.6776890000","TWD97Lat":"24.0996110000"},{"SiteName":"大同","SiteAddress":"臺北市大同區重慶北路三段2號","TWD97Lon":"121.5133110000","TWD97Lat":"25.0632000000"},{"SiteName":"士林","SiteAddress":"臺北市北投區文林北路155號","TWD97Lon":"121.5153890000","TWD97Lat":"25.1054170000"},{"SiteName":"土城","SiteAddress":"新北市土城區學府路一段241號","TWD97Lon":"121.4518610000","TWD97Lat":"24.9825280000"},{"SiteName":"三義","SiteAddress":"苗栗縣三義鄉西湖村上湖61-1號","TWD97Lon":"120.7588330000","TWD97Lat":"24.3829420000"},{"SiteName":"三重","SiteAddress":"新北市三重區三和路重陽路交口","TWD97Lon":"121.4938060000","TWD97Lat":"25.0726110000"},{"SiteName":"二林","SiteAddress":"彰化縣二林鎮萬合里江山巷1號","TWD97Lon":"120.4096530000","TWD97Lat":"23.9251750000"}]; 
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