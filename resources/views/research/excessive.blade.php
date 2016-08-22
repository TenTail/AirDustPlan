@extends('layouts.master')

@section('csrf-token')
<meta name="csrf-token" content="{{ csrf_token() }}" />
@stop

@section('title', '空塵計')

@section('head-javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.1.0/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
@endsection

@section('content')
<style>
.excessive-child {
    margin: 10px 0;
    padding: 0 5px;
    border-style: solid;
    border-width: 1px;
    border-radius: 5px;
}
.title {
    text-align: center;
}
.remove-btn {
    position: absolute;
    right: 0;
    bottom: 0;
}
.excessive-svg {
    width: 100%;
    height: 120px;
}
#excessive {
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    margin: 0;
    font-family: Helvetica;
}
</style>

<div class="col-md-12">
    <h1>好日子與壞日子</h1>
    <h3>此頁面顯示的顏色代表AQI級別，是依照美國標準進行轉換。</h3>
    <table>
        <tr>
            <td><h3 style="color: rgb(00, 255, 0);">綠色：一級（優）</h3></td>
            <td><h3 style="color: rgb(255, 255, 0);">黃色：二級（中等）</h3></td>
            <td><h3 style="color: rgb(255, 150, 00);">橘色：三級（不適於敏感人群）</h3></td>
        </tr>
        <tr>
            <td><h3 style="color: rgb(255, 00, 00);">紅色：四級（不健康）</h3></td>
            <td><h3 style="color: rgb(255, 00, 255);">紫色：五級（重度污染）</h3></td>
            <td><h3 style="color: rgb(0, 0, 0);">黑色：無資料</h3></td>
        </tr>
    </table>
    <div class="col-md-3">
        <h2 style="text-align: center;">選擇年份</h2>
        <select id="year" class="form-control">
            @for ($i = getdate()['year']; $i != 1984; $i--)
                <option value="{{ $i }}">{{ $i."年" }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-3">
        <h2 style="text-align: center;">選擇縣市</h2>
        <select id="county" class="form-control">
            <?php $county = ['新北市', '屏東縣', '臺南市', '宜蘭縣', '嘉義縣', '臺東縣', '澎湖縣', '臺北市', '嘉義市', '臺中市', '雲林縣', '高雄市', '臺北市', '新竹市', '新竹縣', '基隆市', '苗栗縣', '桃園市', '彰化縣', '花蓮縣', '南投縣'];?>
            @for ($i = 0, $length = count($county); $i < $length; $i++)
                <option value="{{ $county[$i] }}">{{ $county[$i] }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-3">
        <h2 style="text-align: center;">選擇測站</h2>
        <select id="sitename" class="form-control">
            {{-- option --}}
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-success" onClick="addExcessive()" style="margin-top: 10px">
            <h2>
                <span class="glyphicon glyphicon-plus" aria-hidden="true">新增比較測站</span>
            </h2>
        </button>
    </div>
</div>
<div class="col-md-12" id="excessive-group" style="padding: 0;">
    {{-- <div class="excessive-child" id="淡水">
        <div style="position: relative;">
            <h2 class="title">2015年-淡水測站</h2>
            <button class="remove-btn btn btn-danger" onClick="removeExcessive('淡水')"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
        </div>
        <div class="excessive-svg" id="excessive-淡水"></div>
    </div> --}}
</div>

@endsection

@section('page-javascript')
<script>
var all_site = [
    {county: '基隆市', sitename: ['基隆']},
    {county: '嘉義市', sitename: ['嘉義']},
    {county: '高雄市', sitename: ['美濃','大寮','橋頭','仁武','鳳山','林園','楠梓','左營','前金','前鎮','小港','復興']},
    {county: '新北市', sitename: ['汐止','萬里','新店','土城','板橋','新莊','菜寮','林口','淡水','三重','永和']},
    {county: '臺北市', sitename: ['士林','中山','萬華','古亭','松山','大同','陽明']},
    {county: '桃園市', sitename: ['桃園','大園','觀音','平鎮','龍潭','中壢']},
    {county: '新竹縣', sitename: ['湖口','竹東']},
    {county: '新竹市', sitename: ['新竹']},
    {county: '苗栗縣', sitename: ['頭份','苗栗','三義']},
    {county: '臺中市', sitename: ['豐原','沙鹿','大里','忠明','西屯']},
    {county: '彰化縣', sitename: ['彰化','線西','二林']},
    {county: '南投縣', sitename: ['南投','竹山','埔里']},
    {county: '雲林縣', sitename: ['斗六','崙背','臺西','麥寮']},
    {county: '嘉義縣', sitename: ['新港','朴子']},
    {county: '臺南市', sitename: ['新營','善化','安南','臺南']},
    {county: '屏東縣', sitename: ['屏東','潮州','恆春']},
    {county: '臺東縣', sitename: ['臺東','關山']},
    {county: '宜蘭縣', sitename: ['宜蘭','冬山']},
    {county: '花蓮縣', sitename: ['花蓮']},
    {county: '澎湖縣', sitename: ['馬公']},
    {county: '連江縣', sitename: ['馬祖']},
    {county: '金門縣', sitename: ['金門']}
];

// search county then return index
function searchSiteIndex(county) {
    for(var i = 0, length1 = all_site.length; i < length1; i++){
        if (all_site[i].county == county) {
            return i;
        }
    }
}

// change sitename option
function loadSite() {
    var index = searchSiteIndex($('#county').val());
    $('#sitename').empty(); // 清空
    // 加入新的<option>
    for(var i = 0, length1 = all_site[index].sitename.length; i < length1; i++){
        var option = $('<option></option>').attr('value', all_site[index].sitename[i]).text(all_site[index].sitename[i]);
        $('#sitename').append(option);
    }
}

// change sitename when county selected
$('#county').change(function () {
    loadSite();
});

// add excessive-group
function addExcessive() {
    var year = $('#year').val();
    var s = $('#sitename').val();
    var ss = "'"+year+s+"'";
    if (document.getElementById(year+s)) {
        alert(year+"年"+s+"已新增");
    } else {
        var html = '<div class="col-md-12 excessive-child" id="'+year+s+'">';
        html = html+'<div style="position: relative;">';
        html = html+'<h2 class="title">'+year+'年-'+s+'測站</h2>';
        html = html+'<button class="remove-btn btn btn-danger" onClick="removeExcessive('+ss+')"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';
        html = html+'</div>';
        html = html+'<div class="excessive-svg" id="excessive-'+year+s+'"></div>';
        html = html+'</div>';
        $('#excessive-group').append(html);

        getSvgData(year, s);
    }
}

// remove excessive-group
function removeExcessive(ss) {
    $('#'+ss).remove();
}

$(document).ready(function () {
    loadSite();
});


// draw data
var level1 ;
var level2 ;
var level3 ;
var level4 ;
var level5 ;
function drawSvg(year, sitename) {
    var date = moment(year+'-01-01','YYYY-MM-DD');
    var dataAll = [];
    var dataSplitByMonth = [];

    while(date.calendar() !== '01/01/'+(parseInt(year)+1)) {
        
        dataAll.push({ 
            date: date.calendar(),
            weekDay: date.day(),
            month: date.month() + 1,
            day: date.date(),
            year: date.year(),
            level1: (level1[date.calendar()]) ? level1[date.calendar()] : false,
            level2: (level2[date.calendar()]) ? level2[date.calendar()] : false,
            level3: (level3[date.calendar()]) ? level3[date.calendar()] : false,
            level4: (level4[date.calendar()]) ? level4[date.calendar()] : false,
            level5: (level5[date.calendar()]) ? level5[date.calendar()] : false,
        });

        date.add(1, 'day');
    }

    //split into months
    var m = ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'];
    m.forEach( function(element, index) {
        dataSplitByMonth.push( {
            name: element,
            month: index+1,
            days: dataAll.filter( (day)=> { return day.month === index+1} )
        });
    });

    //calculate layouts
    // each month becomes a g element

    var dayWidth = 10;
    var dayHeight = 10;
    var dayPadding = 2;

    var monthPadding = 10;
    var currentMonthX = 0;

    var dayOfWeekX = {
        0: 0,                                   // sunday
        1: dayWidth + dayPadding,               // monday 
        2: (dayWidth * 2) + (dayPadding * 2),   // tuesday
        3: (dayWidth * 3) + (dayPadding * 3),   // wendsday
        4: (dayWidth * 4) + (dayPadding * 4),   // thursday
        5: (dayWidth * 5) + (dayPadding * 5),   // friday
        6: (dayWidth * 6) + (dayPadding * 6)    // saturday
    };

    dataSplitByMonth.forEach( function(month) {

        var yPos = 20; //start y
        month.days.forEach( function(day) {
            day.x = dayOfWeekX[day.weekDay];
            day.y = yPos;

            if(day.weekDay === 6) {
                yPos += dayHeight + dayPadding;
            }
        });

        month.dimensions = {
            height: month.days[month.days.length-1].y + dayHeight,
            width: (dayWidth * 7) + (dayPadding * 7)
        };

        month.x = currentMonthX;

        currentMonthX += month.dimensions.width + monthPadding;
    });

    //vis
    var width = $('#excessive-'+year+sitename)[0].clientWidth;
    var height = $('#excessive-'+year+sitename)[0].clientHeight;

    var svg = d3.select('#excessive-'+year+sitename).append('svg')
        .attr('width', width)
        .attr('height', height)
        .style('display', 'block')
        .style('margin-right', 'auto')
        .style('margin-left', 'auto')
        .style('background-color', '#FFFFFF');

    var yearView = svg.append('g');


    var months = yearView.selectAll('g') 
       .data(dataSplitByMonth)
       .enter()
            .append('g')
                .attr('transform', function(d) { return 'translate(' + d.x + ',0)' })

    months.each(function(node) {
        
        d3.select(this)
          .selectAll('rect')
          .data(node.days)
          .enter()
            .append('rect')
                .attr('height', dayWidth)
                .attr('width', dayHeight)
                .attr('x', function(d) { return d.x })
                .attr('y', function(d) { return d.y })
                .attr('fill', function(d) {
                    switch (true) {
                        case (d.level1 !== false):
                            return 'rgb(00, 255, 0)';
                            break;
                        case (d.level2 !== false):
                            return 'rgb(255, 255, 0)';
                            break;
                        case (d.level3 !== false):
                            return 'rgb(255, 150, 00)';
                            break;
                        case (d.level4 !== false):
                            return 'rgb(255, 00, 00)';
                            break;
                        case (d.level5 !== false):
                            return 'rgb(255, 00, 255)';
                            break;
                        default:
                            return 'rgb(0, 0, 0)';
                            break;
                    }
                });

        d3.select(this)
          .append('text')
          .text(function(d) { return d.name })
          .attr("text-anchor", "middle")
          .attr('x', function(d) { return d.dimensions.width/2})
          .attr('y', 10)
          .style("font-family", "Helvetica")
          .style("font-size","14pt")
    });

    yearView.attr('transform', function(d) { return 'translate(' + ((width - yearView.node().getBBox().width) /2)+ ',20)' })
}

function getSvgData(year, sitename) {
        var post_data = {
            _token: $('meta[name=csrf-token]').attr('content'),
            year: $('#year').val(),
            sitename: sitename,
        }
        
        $.ajax({
            type: 'POST',
            url: '{{ route('research.excessive-post') }}',
            data: post_data,
            success: function (data) {
                level1 = JSON.parse(data['level1']);
                level2 = JSON.parse(data['level2']);
                level3 = JSON.parse(data['level3']);
                level4 = JSON.parse(data['level4']);
                level5 = JSON.parse(data['level5']);
                drawSvg(data['year'], sitename);
            },
            error: function () {
                alert("查無資料");
            }
        });
}
    

// //vis 2
// var marginStatsViewTop = 150;

// var statsView = svg.append('g')
//     .attr('transform', 'translate( 0,' + (yearView.node().getBBox().height + marginStatsViewTop) + ')');

// var categories = []

// categories.push( dataAll.filter( (day)=> { 
//     if(day.bankH === true || day.weekDay === 6 || day.weekDay === 0) {
//         return true;
//     } else {
//         return false;
//     }
// }));
// categories.push( dataAll.filter( (day) => { 
    
//     return day.holiday === true 
// }));
// categories.push( dataAll.filter( (day) => { 
    
//     if( !day.holiday && !day.bankH && isWeekDay(day.weekDay)) {
//         return true;
//     } else {
//         return false;
//     }
// }));

// categories[0] = categories[0].map((el)=>{ el.type = 'A'; el.text = 'Off'; return el; });
// categories[1] = categories[1].map((el)=>{ el.type = 'B'; el.text = 'Holidays'; return el; });
// categories[2] = categories[2].map((el)=>{ el.type = 'C'; el.text = 'Working'; return el; });

// categories.sort((a,b) => b.length - a.length);

// var startX = (width - yearView.node().getBBox().width)/2;
// var barPadding = 80;
// var avalWidth = width - ((startX*2) + (barPadding*2));
// var barWidth = avalWidth/3;
// var heightPlusBottomMarg = height - 100;

// var maxLength = d3.max([categories[0].length,categories[1].length,categories[2].length])
// // calc bar heigth;
// var startY = parseInt(statsView.attr('transform').split(',')[1].slice(0,-1));
// var heightScale = d3.scaleLinear()
//     .domain([0,maxLength])
//     .range([0,heightPlusBottomMarg - startY]);

// var yPosScale = d3.scaleLinear()
//     .domain([0,maxLength])
//     .range([heightPlusBottomMarg - startY,0])

// var statsData = [
//     {
//         x: startX,
//         y: yPosScale(categories[0].length),
//         w: barWidth,
//         h: heightScale(categories[0].length),
//         type: categories[0][0].type,
//         startY: yPosScale(3),
//         startH: heightScale(3),
//         text: categories[0][0].text,
//         offsetY: startY,
//         length: categories[0].length
//     },
//      {
//         x: startX + barWidth + barPadding,
//         y: yPosScale(categories[1].length),
//         w: barWidth,
//         h: heightScale(categories[1].length),
//         type: categories[1][0].type,
//         startY: yPosScale(3),
//         startH: heightScale(3),
//         text: categories[1][0].text,
//         offsetY: startY,
//         length: categories[1].length
//     },
//      {
//         x: startX + barWidth + barPadding + barWidth + barPadding,
//         y: yPosScale(categories[2].length),
//         w: barWidth,
//         h: heightScale(categories[2].length),
//         type: categories[2][0].type,
//         startY: yPosScale(3),
//         startH: heightScale(3),
//         text: categories[2][0].text,
//         offsetY: startY,
//         length: categories[2].length
//     }
// ]

// var bars = statsView.selectAll('rect')
//     .data(statsData)
//   .enter()
//     .append('rect')
//         .attr('x', function(d) { return d.x; })
//         .attr('y', function(d) { return d.startY; })
//         .attr('width', function(d) { return d.w; })
//         .attr('height', function(d) { return d.startH; })
//         .attr('fill', function(d) {
//             if(d.type === 'A') return '#EF476F';
//             if(d.type === 'B') return '#FFC43D';
//             if(d.type === 'C') return '#1B9AAA';
//         })

// var barLables = statsView.selectAll('text')
//     .data(statsData)
//   .enter()
//     .append('text')
//         .attr("x", function(d) { return d.x + (d.w/2) } )
//         .attr("y", function(d) { return d.startY + d.startH + 20 } )
//         .attr("text-anchor", "middle")
//         .text(function(d){ return d.text; })
//         .attr('fill', 'black')
//         .style("font-family", "Helvetica")
//         .style("font-size","14pt");

// // movie
// addTemporaryDayAndMoveTo(barLables, function(maxDur) {
    
//     var counter = 0;

//     bars.transition()
//         .duration(maxDur)
//         .attr('y', function(d) { return d.y})
//         .attr('height', function(d) { return d.h})
//         .on('end', function() {
//             counter ++;
//             if(counter === 2) {
//                 console.log('kkk')
//                 statsView.selectAll('text').each(function(p,j) {
//                     d3.select(this.parentNode).append('text')
//                         .attr("x",  p.x + (p.w/2))
//                         .attr("y", p.y + (p.h/2) )
//                         .attr("text-anchor", "middle")
//                         .text( p.length)
//                         .attr('fill', 'transparent')
//                         .style("font-family", "Helvetica")
//                         .style("font-size","12pt")
//                         .transition()
//                             .duration(1000)
//                             .attrTween("fill", function() {
//                                 return d3.interpolateRgb("transparent", "black");
//                             });
//                 })
//             }
//         })
// });



// function addTemporaryDayAndMoveTo(barLables, moveCallback) {
//     var positions = [];
//     //http://stackoverflow.com/questions/6858479/rectangle-coordinates-after-transform
//     yearView.selectAll('rect').each(function(d) {
        
//         var pos = getRelPos(this, svg);

//         pos.cx = pos.x + (dayWidth / 2);
//         pos.cy = pos.y + (dayHeight / 2);
//         pos.color = d3.select(this).attr('fill');
//         pos.type = d.type;
//         positions.push(pos);
//     });

//     function getRelPos(node, svg) {
//         var m = node.getCTM();
//         var pos = svg.node().createSVGPoint();
//         pos.x = d3.select(node).attr('x');
//         pos.y = d3.select(node).attr('y');
        
//         pos = pos.matrixTransform(m);

//         return pos;
//     }

//     var textPos = {};

//     statsView.selectAll('text').each( function(d) {
//         //console.log(d)
//         textPos[d.type] = d;
//     })
    

//     var tempG = svg.append('g');
    
//     //var tempPos = getRelPos(circpack.node(), svg);
//     //var counter = 0;
//     var counter = false;
//     var maxDur = -Infinity;

//     var delayScale = d3.scaleLinear()
//         .domain([0,positions.length])
//         .range([300,2000]);

//     tempG.selectAll('rect')
//          .data(positions)
//          .enter()
//             .append('rect')
//                 .attr('x', function(d) { return d.x })
//                 .attr('y', function(d) { return d.y })
//                 .attr('width', dayWidth)
//                 .attr('height', dayHeight)
//                 .attr('fill', function(d) { return d.color })
//                 .transition()
//                     .delay(function(d,i) { return delayScale(i) })
//                     //.delay(d3.randomUniform(1000, 5000)() )
//                     .attr('x', function(d){
//                         return (textPos[d.type].x + textPos[d.type].w / 2);
//                     })
//                     .attr('y', function(d){
//                         return (textPos[d.type].offsetY + textPos[d.type].startY);
//                     })
//                     .duration(function(d,i) { 
//                         var dur = d3.randomUniform(500, 2000)();

//                         if(dur > maxDur) maxDur = dur;

//                         return dur;
//                     })
//                     //.ease(d3.easeQuadIn)
//                     .on('end', function() {
//                         //counter++
//                         //if(!counter === positions.length) {
//                         if(!counter) {
//                             moveCallback(maxDur);
//                             counter = true;
//                         }
//                     })
//                     .remove();
     
// }


// //Helper functions
// function isWeekDay(num) {
//     var o = {
//         0: false,
//         1: true,
//         2: true,
//         3: true,
//         4: true,
//         5: true,
//         6: false
//     }

//     return o[num];
// }

</script>
@endsection