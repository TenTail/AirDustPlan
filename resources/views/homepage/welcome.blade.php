@extends("layouts.master")

@section("title", "空塵計")

@section("content")

<style>
.title {
    text-align: center;
}
.text {
    font-size: 22px;
    text-indent: 40px;
}
.box img {
    border: 1px black solid;
    /*width: */
    /*float: left;*/
}
</style>

<div class="col-md-12" style="margin-bottom: 10px;">
    <h1 class="title">網站製作動機</h1>
    <hr>
    <p class="text">因應現在民眾對於空氣品質相關議題關注度提升，本專題基於資料探勘技術結合網頁平台、智慧型手機平台以及智慧燈泡開發出一套空氣品質分析系統。</p>
    <p class="text">本專題目的在於以資料探勘技術進行空氣污染來源與空氣污染影響範圍之研究，探討空氣污染與工廠、自然因素等關聯，以及利用物聯網技術進行資料視覺化之研究。研究對象為政府資料開放平台提供之空氣品質即時污染指標、十分鐘雨量、高速公路車流量之資料，樣本時間為 1985 年至 2016年 11 月。</p>
    
    <h1 class="title">網站介紹</h1>
    <hr>

    <div class="box col-md-12">
        <h2>
            <span class="glyphicon glyphicon-object-align-bottom" aria-hidden="true"></span>即時空汙資訊
        </h2>
        <div class="col-md-3">
            <img class="left" src="./img/home1.png" width="200" height="auto" alt="" >
        </div>
        <div class="col-md-9">
            <p class="text">
                利用環保署開放的空氣品質即時污染指標的資料，在Google Map上面依照個測站的位置，顯示當時的空汙數值。
            </p>
        </div>
    </div>
    <div class="box col-md-12">
        <h2>
            <span class="glyphicon glyphicon-hourglass" aria-hidden="true"></span>歷年空汙比較
        </h2>
        <div class="col-md-3">
            <img class="left" src="./img/home2.png" width="200" height="auto" alt="" >
        </div>
        <div class="col-md-9">
            <p class="text">
                在網頁上面透過折現圖表來顯示和比較空汙的歷史資料，選擇完年份後將比較該年與上一年的PM2.5數值。
            </p>
        </div>
    </div>
    <div class="box col-md-12">
        <h2>
            <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>好日子與壞日子
        </h2>
        <div class="col-md-3">
            <img class="left" src="./img/home3.png" width="200" height="auto" alt="" >
        </div>
        <div class="col-md-9">
            <p class="text">
                統計每日空汙的平均值，並使用月曆的方式來顯示資料，可以透過這個方式快速查看該測站整年得空汙狀況。
            </p>
        </div>
    </div>
    <div class="box col-md-12">
        <h2>
            <span class="glyphicon glyphicon-save" aria-hidden="true"></span>歷史資料下載
        </h2>
        <div class="col-md-3">
            <img class="left" src="./img/home4.png" width="200" height="auto" alt="" >
        </div>
        <div class="col-md-9">
            <p class="text">
                網站提供管理員上傳空汙的歷史資料，並讓使用者可下載已處理完畢的歷史資料，提供csv、xls、json檔案格式。
            </p>
        </div>
    </div>   

    <div class="col-md-12" style="font-size: 18px;">
        
        <h1 class="title" style="">聯絡我們</h1>
        <hr>
       
        {!! Form::open(['action' => 'HomePageController@contactus', 'method' => 'post']) !!}
            <div class="form-group">
                {!! Form::label('name', '姓名') !!}
                {!! Form::text('name', null, ['id' => 'name', 'class' =>'form-control', 'placeholder' => 'Name']) !!} 
                {!! Form::label('email', 'Email')!!}
                {!! Form::email('email', null, ['id' => 'email', 'class' =>'form-control', 'placeholder' => 'example@gmail.com']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('content', '問題描述')!!}
                {!! Form::textarea('content', null, ['id' => 'content', 'class' => 'form-control']) !!}
            </div> 

            <div class="form-group">
                {!! Form::submit('送出', ['class' => 'btn btn-success form-control'])!!}
            </div>
        {!! Form::close() !!}
    </div>
</div>

@endsection