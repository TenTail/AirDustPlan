@extends('layouts.master')

@section('title', '空塵計')

@section('head-javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.1.0/d3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.14.1/moment.min.js"></script>
@endsection

@section('content')
<style>
#excessive {
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  margin: 0;
  font-family: Helvetica;
}
</style>

<div style="width: 100%; height: 700px" id="excessive"></div>

@endsection

@section('page-javascript')
<script>
var level1 = {!! json_encode($level1) !!};
var level2 = {!! json_encode($level2) !!};
var level3 = {!! json_encode($level3) !!};
var level4 = {!! json_encode($level4) !!};
var level5 = {!! json_encode($level5) !!};

var bankHolidays = {
    '01/01/2015': true,
};

var myHolidays = {
    '01/04/2015': true,
    '01/05/2015': true,
};

var date = moment('2015-01-01','YYYY-MM-DD');
var dataAll = [];
var dataSplitByMonth = [];

while(date.calendar() !== '01/01/2016') {
    
    dataAll.push({ 
        date: date.calendar(),
        weekDay: date.day(),
        month: date.month() + 1,
        day: date.date(),
        year: date.year(),
        bankH: (bankHolidays[date.calendar()] === true) ? true : false,
        holiday: (myHolidays[date.calendar()] === true) ? true : false
    });

    date.add(1, 'day');
}
//
console.log(dataAll);

//split into months
dataSplitByMonth.push( {
    name: '一月',
    month: 1,
    days: dataAll.filter( (day)=> { return day.month === 1} )
});
dataSplitByMonth.push( {
    name: '二月',
    month: 2,
    days: dataAll.filter( (day)=> { return day.month === 2} )
});
dataSplitByMonth.push( {
    name: '三月',
    month: 3,
    days: dataAll.filter( (day)=> { return day.month === 3} )
});
dataSplitByMonth.push( {
    name: '四月',
    month: 4,
    days: dataAll.filter( (day)=> { return day.month === 4} )
});
dataSplitByMonth.push( {
    name: '五月',
    month: 5,
    days: dataAll.filter( (day)=> { return day.month === 5} )
});
dataSplitByMonth.push( {
    name: '六月',
    month: 6,
    days: dataAll.filter( (day)=> { return day.month === 6} )
});
dataSplitByMonth.push( {
    name: '七月',
    month: 7,
    days: dataAll.filter( (day)=> { return day.month === 7} )
});
dataSplitByMonth.push( {
    name: '八月',
    month: 8,
    days: dataAll.filter( (day)=> { return day.month === 8} )
});
dataSplitByMonth.push( {
    name: '九月',
    month: 9,
    days: dataAll.filter( (day)=> { return day.month === 9} )
});
dataSplitByMonth.push( {
    name: '十月',
    month: 10,
    days: dataAll.filter( (day)=> { return day.month === 10} )
});
dataSplitByMonth.push( {
    name: '十一月',
    month: 11,
    days: dataAll.filter( (day)=> { return day.month === 11} )
});
dataSplitByMonth.push( {
    name: '十二月',
    month: 12,
    days: dataAll.filter( (day)=> { return day.month === 12} )
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
var width = $('#excessive')[0].clientWidth;
var height = $('#excessive')[0].clientHeight;

var svg = d3.select('#excessive').append('svg')
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
                if(d.bankH || d.weekDay === 0 || d.weekDay === 6) {
                    return '#EF476F';
                } else if(d.holiday) {
                    return '#FFC43D';
                } else {
                    return '#1B9AAA';
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
    

//vis 2
var marginStatsViewTop = 150;

var statsView = svg.append('g')
    .attr('transform', 'translate( 0,' + (yearView.node().getBBox().height + marginStatsViewTop) + ')');

var categories = []

categories.push( dataAll.filter( (day)=> { 
    if(day.bankH === true || day.weekDay === 6 || day.weekDay === 0) {
        return true;
    } else {
        return false;
    }
}));
categories.push( dataAll.filter( (day) => { 
    
    return day.holiday === true 
}));
categories.push( dataAll.filter( (day) => { 
    
    if( !day.holiday && !day.bankH && isWeekDay(day.weekDay)) {
        return true;
    } else {
        return false;
    }
}));

categories[0] = categories[0].map((el)=>{ el.type = 'A'; el.text = 'Off'; return el; });
categories[1] = categories[1].map((el)=>{ el.type = 'B'; el.text = 'Holidays'; return el; });
categories[2] = categories[2].map((el)=>{ el.type = 'C'; el.text = 'Working'; return el; });

categories.sort((a,b) => b.length - a.length);
console.log(categories);
var startX = (width - yearView.node().getBBox().width)/2;
var barPadding = 80;
var avalWidth = width - ((startX*2) + (barPadding*2));
var barWidth = avalWidth/3;
var heightPlusBottomMarg = height - 100;

var maxLength = d3.max([categories[0].length,categories[1].length,categories[2].length])
// calc bar heigth;
var startY = parseInt(statsView.attr('transform').split(',')[1].slice(0,-1));
var heightScale = d3.scaleLinear()
    .domain([0,maxLength])
    .range([0,heightPlusBottomMarg - startY]);

var yPosScale = d3.scaleLinear()
    .domain([0,maxLength])
    .range([heightPlusBottomMarg - startY,0])

var statsData = [
    {
        x: startX,
        y: yPosScale(categories[0].length),
        w: barWidth,
        h: heightScale(categories[0].length),
        type: categories[0][0].type,
        startY: yPosScale(3),
        startH: heightScale(3),
        text: categories[0][0].text,
        offsetY: startY,
        length: categories[0].length
    },
     {
        x: startX + barWidth + barPadding,
        y: yPosScale(categories[1].length),
        w: barWidth,
        h: heightScale(categories[1].length),
        type: categories[1][0].type,
        startY: yPosScale(3),
        startH: heightScale(3),
        text: categories[1][0].text,
        offsetY: startY,
        length: categories[1].length
    },
     {
        x: startX + barWidth + barPadding + barWidth + barPadding,
        y: yPosScale(categories[2].length),
        w: barWidth,
        h: heightScale(categories[2].length),
        type: categories[2][0].type,
        startY: yPosScale(3),
        startH: heightScale(3),
        text: categories[2][0].text,
        offsetY: startY,
        length: categories[2].length
    }
]

var bars = statsView.selectAll('rect')
    .data(statsData)
  .enter()
    .append('rect')
        .attr('x', function(d) { return d.x; })
        .attr('y', function(d) { return d.startY; })
        .attr('width', function(d) { return d.w; })
        .attr('height', function(d) { return d.startH; })
        .attr('fill', function(d) {
            if(d.type === 'A') return '#EF476F';
            if(d.type === 'B') return '#FFC43D';
            if(d.type === 'C') return '#1B9AAA';
        })

var barLables = statsView.selectAll('text')
    .data(statsData)
  .enter()
    .append('text')
        .attr("x", function(d) { return d.x + (d.w/2) } )
        .attr("y", function(d) { return d.startY + d.startH + 20 } )
        .attr("text-anchor", "middle")
        .text(function(d){ return d.text; })
        .attr('fill', 'black')
        .style("font-family", "Helvetica")
        .style("font-size","14pt");

// movie
addTemporaryDayAndMoveTo(barLables, function(maxDur) {
    
    var counter = 0;

    bars.transition()
        .duration(maxDur)
        .attr('y', function(d) { return d.y})
        .attr('height', function(d) { return d.h})
        .on('end', function() {
            counter ++;
            if(counter === 2) {
                console.log('kkk')
                statsView.selectAll('text').each(function(p,j) {
                    d3.select(this.parentNode).append('text')
                        .attr("x",  p.x + (p.w/2))
                        .attr("y", p.y + (p.h/2) )
                        .attr("text-anchor", "middle")
                        .text( p.length)
                        .attr('fill', 'transparent')
                        .style("font-family", "Helvetica")
                        .style("font-size","12pt")
                        .transition()
                            .duration(1000)
                            .attrTween("fill", function() {
                                return d3.interpolateRgb("transparent", "black");
                            });
                })
            }
        })
});



function addTemporaryDayAndMoveTo(barLables, moveCallback) {
    var positions = [];
    //http://stackoverflow.com/questions/6858479/rectangle-coordinates-after-transform
    yearView.selectAll('rect').each(function(d) {
        
        var pos = getRelPos(this, svg);

        pos.cx = pos.x + (dayWidth / 2);
        pos.cy = pos.y + (dayHeight / 2);
        pos.color = d3.select(this).attr('fill');
        pos.type = d.type;
        positions.push(pos);
    });

    function getRelPos(node, svg) {
        var m = node.getCTM();
        var pos = svg.node().createSVGPoint();
        pos.x = d3.select(node).attr('x');
        pos.y = d3.select(node).attr('y');
        
        pos = pos.matrixTransform(m);

        return pos;
    }

    var textPos = {};

    statsView.selectAll('text').each( function(d) {
        //console.log(d)
        textPos[d.type] = d;
    })
    

    var tempG = svg.append('g');
    
    //var tempPos = getRelPos(circpack.node(), svg);
    //var counter = 0;
    var counter = false;
    var maxDur = -Infinity;

    var delayScale = d3.scaleLinear()
        .domain([0,positions.length])
        .range([300,2000]);

    tempG.selectAll('rect')
         .data(positions)
         .enter()
            .append('rect')
                .attr('x', function(d) { return d.x })
                .attr('y', function(d) { return d.y })
                .attr('width', dayWidth)
                .attr('height', dayHeight)
                .attr('fill', function(d) { return d.color })
                .transition()
                    .delay(function(d,i) { return delayScale(i) })
                    //.delay(d3.randomUniform(1000, 5000)() )
                    .attr('x', function(d){
                        return (textPos[d.type].x + textPos[d.type].w / 2);
                    })
                    .attr('y', function(d){
                        return (textPos[d.type].offsetY + textPos[d.type].startY);
                    })
                    .duration(function(d,i) { 
                        var dur = d3.randomUniform(500, 2000)();

                        if(dur > maxDur) maxDur = dur;

                        return dur;
                    })
                    //.ease(d3.easeQuadIn)
                    .on('end', function() {
                        //counter++
                        //if(!counter === positions.length) {
                        if(!counter) {
                            moveCallback(maxDur);
                            counter = true;
                        }
                    })
                    .remove();
     
}


//Helper functions
function isWeekDay(num) {
    var o = {
        0: false,
        1: true,
        2: true,
        3: true,
        4: true,
        5: true,
        6: false
    }

    return o[num];
}
</script>
@endsection